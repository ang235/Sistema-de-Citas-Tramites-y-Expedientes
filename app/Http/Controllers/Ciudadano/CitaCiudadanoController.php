<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ciudadano\StoreCitaRequest;
use App\Models\Bitacora;
use App\Models\Cita;
use App\Models\Solicitud;
use App\Services\CitaService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CitaCiudadanoController extends Controller
{
    public function index(CitaService $citaService): View
    {
        $citaService->cancelarCitasVencidas();

        $idUsuario = (int) Auth::id();

        $citas = Cita::where('ID_Usuarios', $idUsuario)
            ->with(['solicitud.folio', 'solicitud.versionTramite.tipoTramite'])
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
            ->with(['solicitud.folio', 'solicitud.versionTramite.tipoTramite'])
            ->first();

        // 2. Filtrar expedientes del usuario: solo aquellos con recaudos aprobados por el revisor
        $idSolicitud = $request->query('id_solicitud') ? (int) $request->query('id_solicitud') : null;
        $solicitudes = Solicitud::where('ID_Solicitante', $idUsuario)
            ->with(['folio', 'versionTramite.tipoTramite', 'documentos', 'asignaciones.revisiones'])
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

        // Calcular ventana de 15 días posteriores a la aprobación documental
        $minFecha = today()->format('Y-m-d');
        $maxFecha = today()->addDays(15)->format('Y-m-d');

        if ($solicitudSeleccionada) {
            $fechaAprobacion = $solicitudSeleccionada->fechaAprobacionDocumentos();
            if ($fechaAprobacion) {
                $limiteAprobacion = $fechaAprobacion->copy()->addDays(15);
                // Si la aprobación es anterior, la fecha máxima no puede superar los 15 días tras dicha aprobación
                $maxFecha = $limiteAprobacion->format('Y-m-d');
            }
        }

        // Mapear horarios ocupados por fecha para deshabilitar en el selector visual
        $citasOcupadas = Cita::where('Estado_Citas', '!=', 'Cancelada')
            ->whereDate('Fecha', '>=', today())
            ->get(['Fecha', 'Hora'])
            ->groupBy(function ($c) {
                return $c->Fecha ? Carbon::parse($c->Fecha)->format('Y-m-d') : '';
            })
            ->map(function ($group) {
                return $group->map(fn ($c) => substr((string) $c->Hora, 0, 5))->unique()->values();
            });

        return view('public.citas.create', [
            'citaActiva' => $citaActiva,
            'idSolicitudSeleccionada' => $idSolicitud,
            'solicitudSeleccionada' => $solicitudSeleccionada,
            'solicitudes' => $solicitudes,
            'minFecha' => $minFecha,
            'maxFecha' => $maxFecha,
            'citasOcupadas' => $citasOcupadas,
        ]);
    }

    public function store(StoreCitaRequest $request): RedirectResponse
    {
        $idUsuario = (int) Auth::id();
        $idSolicitud = (int) $request->validated('id_solicitud');
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
            ->with(['folio', 'documentos', 'asignaciones.revisiones'])
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

        // 4. Prohibir citas duplicadas en el mismo horario y fecha
        $fechaStr = $fechaCarbon->format('Y-m-d');
        $existe = Cita::whereDate('Fecha', $fechaStr)
            ->where(function ($q) use ($horaInput): void {
                $q->where('Hora', $horaInput)
                    ->orWhere('Hora', 'like', "{$horaInput}%");
            })
            ->where('Estado_Citas', '!=', 'Cancelada')
            ->exists();

        if ($existe) {
            return back()->withErrors(['hora' => 'El horario y fecha seleccionados ya se encuentran ocupados. Por favor elija otro horario disponible.'])->withInput();
        }

        $cita = Cita::create([
            'ID_Usuarios' => $idUsuario,
            'ID_Solicitud' => $idSolicitud,
            'Fecha' => $fechaStr,
            'Hora' => $horaInput,
            'Estado_Citas' => 'Programada',
        ]);

        $folioTexto = $solicitud->folio?->Codigo_Folio ?? "EXP-{$idSolicitud}";
        Bitacora::create([
            'ID_Usuarios' => $idUsuario,
            'Accion' => 'Agendación de cita',
            'Descripcion' => "Cita agendada para el expediente {$folioTexto} el {$fechaStr} a las {$horaInput}.",
            'Fecha_Creacion' => now(),
        ]);

        return redirect()->route('citas.pase', ['idCita' => $cita->ID_Citas])
            ->with('success', 'Tu cita ha sido confirmada exitosamente.');
    }

    public function paseDigital(int $idCita): View
    {
        /** @var Cita $cita */
        $cita = Cita::with(['usuario', 'solicitud.folio', 'solicitud.versionTramite.tipoTramite'])
            ->where('ID_Usuarios', Auth::id())
            ->findOrFail($idCita);

        return view('public.citas.pase', [
            'cita' => $cita,
        ]);
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
