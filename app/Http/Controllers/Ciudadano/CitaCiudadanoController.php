<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Http\Middleware\RedirigirLocalhostAIp;
use App\Http\Requests\Ciudadano\StoreCitaRequest;
use App\Models\Bitacora;
use App\Models\Cita;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Services\CitaService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;
use Symfony\Component\HttpFoundation\Response;

class CitaCiudadanoController extends Controller
{
    public function index(CitaService $citaService): View
    {
        $citaService->cancelarCitasVencidas();

        $idUsuario = (int) Auth::id();

        $citas = Cita::where('ID_Usuarios', $idUsuario)
            ->with(['solicitud.folio', 'solicitud.versionTramite.tipoTramite', 'revisor'])
            ->orderBy('Fecha', 'desc')
            ->orderBy('Hora', 'desc')
            ->paginate(10);

        return view('public.citas.index', [
            'citas' => $citas,
        ]);
    }

    public function create(Request $request): View
    {
        $idUsuario = (int) Auth::id();

        // 1. Verificar si el usuario ya tiene una cita activa programada (Solo una a la vez)
        $citaActiva = Cita::where('ID_Usuarios', $idUsuario)
            ->whereIn('Estado_Citas', ['Programada', 'Confirmada'])
            ->whereDate('Fecha', '>=', today())
            ->with(['solicitud.folio', 'solicitud.versionTramite.tipoTramite', 'revisor'])
            ->first();

        // 2. Filtrar expedientes del usuario: solo aquellos con recaudos aprobados por el revisor
        $idSolicitud = $request->query('id_solicitud') ? (int) $request->query('id_solicitud') : null;
        $solicitudes = Solicitud::where('ID_Solicitante', $idUsuario)
            ->with(['folio', 'versionTramite.tipoTramite', 'documentos', 'asignaciones.revisiones', 'asignaciones.revisor'])
            ->get()
            ->filter(fn (Solicitud $sol) => $sol->tieneDocumentosAprobados())
            ->values();

        // Determinar solicitud seleccionada
        $solicitudSeleccionada = null;
        if ($idSolicitud) {
            $solicitudSeleccionada = $solicitudes->firstWhere('ID_Solicitud', $idSolicitud);
        }
        if (! $solicitudSeleccionada && $solicitudes->isNotEmpty()) {
            $solicitudSeleccionada = $solicitudes->first();
            $idSolicitud = $solicitudSeleccionada->ID_Solicitud;
        }

        // Catálogo de revisores técnicos disponibles
        $revisores = Usuario::whereHas('roles', function ($q): void {
            $q->where('Nombre', 'like', '%Revisor%')
                ->orWhere('Nombre', 'like', '%Supervisor%');
        })->where('Activo', true)->orderBy('Nombre')->get();

        // Revisor técnico asignado automáticamente según quién revisó y aprobó cada expediente
        $revisoresPorSolicitud = [];
        foreach ($solicitudes as $sol) {
            $rev = $sol->revisorAprobador() ?? $revisores->first();
            $revisoresPorSolicitud[$sol->ID_Solicitud] = [
                'id' => $rev?->ID_Usuarios,
                'nombre' => $rev?->Nombre ?? 'Revisor Técnico Institucional',
                'email' => $rev?->Email ?? '',
                'rol' => $rev?->roles->first()?->Nombre ?? 'Revisor Técnico',
            ];
        }

        $revisorDefault = $solicitudSeleccionada
            ? ($revisoresPorSolicitud[$solicitudSeleccionada->ID_Solicitud] ?? null)
            : ($revisoresPorSolicitud[$solicitudes->first()?->ID_Solicitud ?? 0] ?? null);

        $idRevisorDefault = $revisorDefault['id'] ?? $revisores->first()?->ID_Usuarios;

        // Calcular ventana de 15 días posteriores a la aprobación documental
        $minFecha = today()->format('Y-m-d');
        $maxFecha = today()->addDays(15)->format('Y-m-d');

        if ($solicitudSeleccionada) {
            $fechaAprobacion = $solicitudSeleccionada->fechaAprobacionDocumentos();
            if ($fechaAprobacion) {
                $limiteAprobacion = $fechaAprobacion->copy()->addDays(15);
                $maxFecha = $limiteAprobacion->format('Y-m-d');
            }
        }

        // Mapear horarios ocupados por fecha y agrupados por cada revisor específico
        $citasOcupadasPorRevisor = [];
        foreach ($revisores as $rev) {
            $citasOcupadasPorRevisor[$rev->ID_Usuarios] = [];
        }

        $citasActivas = Cita::where('Estado_Citas', '!=', 'Cancelada')
            ->whereDate('Fecha', '>=', today())
            ->whereNotNull('ID_Revisor')
            ->get(['ID_Revisor', 'Fecha', 'Hora']);

        foreach ($citasActivas as $c) {
            $fechaKey = $c->Fecha ? Carbon::parse($c->Fecha)->format('Y-m-d') : '';
            $horaKey = substr((string) $c->Hora, 0, 5);
            if ($fechaKey && $c->ID_Revisor) {
                $citasOcupadasPorRevisor[$c->ID_Revisor][$fechaKey][] = $horaKey;
            }
        }

        foreach ($citasOcupadasPorRevisor as $revId => $fechas) {
            foreach ($fechas as $f => $horas) {
                $citasOcupadasPorRevisor[$revId][$f] = array_values(array_unique($horas));
            }
        }

        return view('public.citas.create', [
            'citaActiva' => $citaActiva,
            'idSolicitudSeleccionada' => $idSolicitud,
            'solicitudSeleccionada' => $solicitudSeleccionada,
            'solicitudes' => $solicitudes,
            'revisores' => $revisores,
            'revisoresPorSolicitud' => $revisoresPorSolicitud,
            'idRevisorDefault' => $idRevisorDefault,
            'revisorDefault' => $revisorDefault,
            'minFecha' => $minFecha,
            'maxFecha' => $maxFecha,
            'citasOcupadasPorRevisor' => $citasOcupadasPorRevisor,
        ]);
    }

    public function store(StoreCitaRequest $request): RedirectResponse
    {
        $idUsuario = (int) Auth::id();
        $idSolicitud = (int) $request->validated('id_solicitud');
        $idRevisorInput = $request->validated('id_revisor') ? (int) $request->validated('id_revisor') : null;
        $fechaInput = $request->validated('fecha');
        $horaInput = substr((string) $request->validated('hora'), 0, 5);

        // 1. Regla: Solo una cita activa a la vez
        $citaActiva = Cita::where('ID_Usuarios', $idUsuario)
            ->whereIn('Estado_Citas', ['Programada', 'Confirmada'])
            ->whereDate('Fecha', '>=', today())
            ->first();

        if ($citaActiva) {
            $fechaCita = Carbon::parse($citaActiva->Fecha)->translatedFormat('l, d \d\e F \d\e Y');

            return back()->withErrors([
                'cita' => "Actualmente ya cuentas con una cita programada para el {$fechaCita} a las {$citaActiva->Hora} hrs. Solo se permite tener una cita activa a la vez.",
            ])->withInput();
        }

        // 2. Regla: La cita solo se puede agendar para un expediente con documentos aprobados por el revisor
        /** @var Solicitud $solicitud */
        $solicitud = Solicitud::where('ID_Solicitante', $idUsuario)
            ->with(['folio', 'documentos', 'asignaciones.revisiones', 'asignaciones.revisor'])
            ->findOrFail($idSolicitud);

        if (! $solicitud->tieneDocumentosAprobados()) {
            return back()->withErrors([
                'id_solicitud' => 'Las citas solo pueden agendarse después de que el revisor técnico haya calificado y aprobado los documentos de su expediente.',
            ])->withInput();
        }

        // 3. Regla: Solo se puede agendar dentro de los 15 días posteriores a la aprobación
        $fechaCarbon = Carbon::parse($fechaInput)->startOfDay();
        if ($fechaCarbon->lt(today())) {
            return back()->withErrors(['fecha' => 'No es posible agendar citas en fechas anteriores a la actual.'])->withInput();
        }

        $fechaAprobacion = $solicitud->fechaAprobacionDocumentos();
        $fechaLimite = $fechaAprobacion ? $fechaAprobacion->copy()->addDays(15)->endOfDay() : today()->addDays(15)->endOfDay();

        if ($fechaCarbon->gt($fechaLimite)) {
            $limiteEs = $fechaLimite->translatedFormat('d \d\e F \d\e Y');

            return back()->withErrors([
                'fecha' => "Después de la aprobación documental solo es posible agendar dentro de los próximos 15 días. Su fecha límite es el {$limiteEs}.",
            ])->withInput();
        }

        // Regla: El revisor se asigna AUTOMÁTICAMENTE según quién revisó y aprobó los documentos del expediente
        $revisorAprobador = $solicitud->revisorAprobador();
        $idRevisor = $revisorAprobador?->ID_Usuarios;

        // Fallback en caso de expedientes sin asignación previa registrada
        if (! $idRevisor) {
            $idRevisor = $idRevisorInput ?? Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Revisor%'))
                ->where('Activo', true)
                ->value('ID_Usuarios');
        }

        $fechaStr = $fechaCarbon->format('Y-m-d');

        // 4. Prohibir citas duplicadas para el MISMO revisor en el mismo horario y fecha
        if ($idRevisor) {
            $existeRevisorOcupado = Cita::where('ID_Revisor', $idRevisor)
                ->whereDate('Fecha', $fechaStr)
                ->where(function ($q) use ($horaInput): void {
                    $q->where('Hora', $horaInput)
                        ->orWhere('Hora', 'like', "{$horaInput}%");
                })
                ->where('Estado_Citas', '!=', 'Cancelada')
                ->exists();

            if ($existeRevisorOcupado) {
                $nombreRevisor = $revisorAprobador?->Nombre ?? 'asignado';

                return back()->withErrors(['hora' => "El especialista técnico que dictaminó su expediente ({$nombreRevisor}) ya cuenta con una cita en ese horario. Por favor elija otro horario disponible."])->withInput();
            }
        } else {
            $existeGeneral = Cita::whereNull('ID_Revisor')
                ->whereDate('Fecha', $fechaStr)
                ->where(function ($q) use ($horaInput): void {
                    $q->where('Hora', $horaInput)
                        ->orWhere('Hora', 'like', "{$horaInput}%");
                })
                ->where('Estado_Citas', '!=', 'Cancelada')
                ->exists();

            if ($existeGeneral) {
                return back()->withErrors(['hora' => 'El horario y fecha seleccionados ya se encuentran ocupados. Por favor elija otro horario disponible.'])->withInput();
            }
        }

        $cita = Cita::create([
            'ID_Usuarios' => $idUsuario,
            'ID_Solicitud' => $idSolicitud,
            'ID_Revisor' => $idRevisor,
            'Fecha' => $fechaStr,
            'Hora' => $horaInput,
            'Estado_Citas' => 'Programada',
        ]);

        $revisorObj = $idRevisor ? Usuario::find($idRevisor) : null;
        $folioTexto = $solicitud->folio?->Codigo_Folio ?? "EXP-{$idSolicitud}";
        Bitacora::create([
            'ID_Usuarios' => $idUsuario,
            'Accion' => 'Agendación de cita',
            'Descripcion' => "Cita agendada para el expediente {$folioTexto} el {$fechaStr} a las {$horaInput} con el revisor: ".($revisorObj?->Nombre ?? 'Sin asignar').'.',
            'Fecha_Creacion' => now(),
        ]);

        return redirect()->route('citas.pase', ['idCita' => $cita->ID_Citas])
            ->with('success', 'Tu cita ha sido confirmada exitosamente.');
    }

    public function paseDigital(int $idCita): View
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $query = Cita::with(['usuario', 'solicitud.folio', 'solicitud.versionTramite.tipoTramite', 'revisor']);

        if ($usuario->esCiudadano()) {
            $query->where('ID_Usuarios', $usuario->ID_Usuarios);
        }

        /** @var Cita $cita */
        $cita = $query->findOrFail($idCita);

        $qrHost = request()->getHost();
        $qrUrl = route('citas.pase', ['idCita' => $cita->ID_Citas]);
        if (($qrHost === 'localhost' || $qrHost === '127.0.0.1') && $lanIp = RedirigirLocalhostAIp::obtenerIpRedActiva()) {
            $qrUrl = preg_replace('#^https?://(localhost|127\.0\.0\.1)#', 'http://'.$lanIp, $qrUrl);
        }

        return view('public.citas.pase', [
            'cita' => $cita,
            'qrUrl' => $qrUrl,
        ]);
    }

    public function pasePdf(Request $request, int $idCita): Response|PdfBuilder
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $query = Cita::with(['usuario', 'solicitud.folio', 'solicitud.versionTramite.tipoTramite', 'revisor']);

        if ($usuario?->esCiudadano()) {
            $query->where('ID_Usuarios', $usuario->ID_Usuarios);
        }

        /** @var Cita $cita */
        $cita = $query->findOrFail($idCita);

        $qrHost = request()->getHost();
        $qrUrl = route('citas.pase', ['idCita' => $cita->ID_Citas]);
        if (($qrHost === 'localhost' || $qrHost === '127.0.0.1') && $lanIp = RedirigirLocalhostAIp::obtenerIpRedActiva()) {
            $qrUrl = preg_replace('#^https?://(localhost|127\.0\.0\.1)#', 'http://'.$lanIp, $qrUrl);
        }

        $turno = str_pad((string) $cita->ID_Citas, 5, '0', STR_PAD_LEFT);

        $pdf = Pdf::view('pdf.pase-cita', [
            'cita' => $cita,
            'qrUrl' => $qrUrl,
        ])
            ->format('a4')
            ->name("SETC-Pase-Turno-{$turno}.pdf");

        return $request->boolean('descargar') ? $pdf->download() : $pdf;
    }

    public function cancelar(int $idCita): RedirectResponse
    {
        /** @var Cita $cita */
        $cita = Cita::where('ID_Usuarios', Auth::id())->findOrFail($idCita);

        $cita->update(['Estado_Citas' => 'Cancelada']);

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Cancelación de cita',
            'Descripcion' => "Cita #{$cita->ID_Citas} cancelada por el solicitante.",
            'Fecha_Creacion' => now(),
        ]);

        return redirect()->route('citas.index')->with('info', 'La cita fue cancelada correctamente.');
    }
}
