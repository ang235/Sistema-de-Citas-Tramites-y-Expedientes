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
        $idSolicitud = $request->query('id_solicitud');
        $solicitudes = Solicitud::where('ID_Solicitante', Auth::id())
            ->with(['folio', 'versionTramite.tipoTramite'])
            ->get();

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
            'idSolicitudSeleccionada' => $idSolicitud,
            'solicitudes' => $solicitudes,
            'citasOcupadas' => $citasOcupadas,
        ]);
    }

    public function store(StoreCitaRequest $request): RedirectResponse
    {
        $idUsuario = (int) Auth::id();
        $fechaInput = $request->validated('fecha');
        $horaInput = substr((string) $request->validated('hora'), 0, 5);

        // 1. Prohibir estrictamente citas en fechas anteriores
        $fechaCarbon = Carbon::parse($fechaInput)->startOfDay();
        if ($fechaCarbon->isPast() && ! $fechaCarbon->isToday()) {
            return back()->withErrors(['fecha' => 'No es posible agendar citas en fechas anteriores a la actual.'])->withInput();
        }

        // 2. Prohibir citas duplicadas en el mismo horario y fecha
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
            'ID_Solicitud' => $request->validated('id_solicitud'),
            'Fecha' => $fechaStr,
            'Hora' => $horaInput,
            'Estado_Citas' => 'Programada',
        ]);

        Bitacora::create([
            'ID_Usuarios' => $idUsuario,
            'Accion' => 'Agendación de cita',
            'Descripcion' => "Cita agendada para {$fechaStr} a las {$horaInput}.",
            'Fecha_Creacion' => now(),
        ]);

        return redirect()->route('citas.pase', ['idCita' => $cita->ID_Citas])
            ->with('success', 'Tu cita ha sido confirmada.');
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
