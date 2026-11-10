<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Resolucion;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardOperativoController extends Controller
{
    public function index(): View|RedirectResponse
    {
        /** @var Usuario|null $usuario */
        $usuario = Auth::user();

        if ($usuario && $usuario->esCiudadano()) {
            return redirect()->route('expedientes.index');
        }

        if ($usuario && $usuario->esRevisor() && ! $usuario->esSupervisor() && ! $usuario->esAdministrador()) {
            return redirect()->route('portal.revisor-dashboard');
        }

        if ($usuario && $usuario->esSupervisor() && ! $usuario->esAdministrador()) {
            return redirect()->route('portal.supervisor-dashboard');
        }

        $idUsuario = (int) Auth::id();

        $metricas = [
            'total_solicitudes' => Solicitud::count(),
            'pendientes_asignar' => Solicitud::doesntHave('asignaciones')->count(),
            'en_revision' => Solicitud::where('Estado_Solicitud', 'En_Revision')->count(),
            'con_observaciones' => Solicitud::where('Estado_Solicitud', 'Observado')->count(),
            'aprobadas' => Solicitud::where('Estado_Solicitud', 'Aprobado')->count(),
            'citas_hoy' => Cita::whereDate('Fecha', today())->where('Estado_Citas', 'Programada')->count(),
            'mis_asignadas' => Solicitud::whereHas('asignaciones', function ($q) use ($idUsuario): void {
                $q->where('ID_Revisor', $idUsuario);
            })->count(),
        ];

        // Expedientes recientes
        $solicitudesRecientes = Solicitud::with([
            'solicitante',
            'versionTramite.tipoTramite',
            'folio',
            'asignaciones.revisor',
        ])
            ->latest('ID_Solicitud')
            ->take(10)
            ->get();

        return view('portal.dashboard', [
            'metricas' => $metricas,
            'solicitudesRecientes' => $solicitudesRecientes,
        ]);
    }

    public function supervisorIndex(): View|RedirectResponse
    {
        /** @var Usuario|null $usuario */
        $usuario = Auth::user();

        // Ventana exclusiva de Supervisión
        if (! $usuario || ! ($usuario->esSupervisor() || $usuario->esAdministrador())) {
            if ($usuario?->esRevisor()) {
                return redirect()->route('portal.revisor-dashboard');
            }

            return redirect()->route('expedientes.index');
        }

        // ── Si es Supervisor / Administrador: Mando de Supervisión General ──
        $metricas = [
            'sin_asignar' => Solicitud::doesntHave('asignaciones')->count(),
            'en_evaluacion' => Solicitud::where('Estado_Solicitud', 'En_Revision')->count(),
            'con_observaciones' => Solicitud::where('Estado_Solicitud', 'Observado')->count(),
            'pendientes_resolucion' => Solicitud::whereIn('Estado_Solicitud', ['Aprobado', 'Rechazado'])
                ->doesntHave('resoluciones')
                ->count(),
            'resoluciones_emitidas' => Resolucion::count(),
            'revisores_activos' => Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Revisor%'))->count(),
        ];

        // Expedientes que requieren asignación urgente
        $expedientesPorAsignar = Solicitud::doesntHave('asignaciones')
            ->with(['solicitante', 'versionTramite.tipoTramite', 'folio'])
            ->latest('ID_Solicitud')
            ->take(8)
            ->get();

        // Expedientes para supervisión de dictamen y emisión de resolución
        $expedientesSupervision = Solicitud::whereHas('asignaciones')
            ->with([
                'solicitante',
                'versionTramite.tipoTramite',
                'folio',
                'asignaciones.revisor',
                'resoluciones',
            ])
            ->latest('updated_at')
            ->take(10)
            ->get();

        // Carga operativa actual de los revisores técnicos
        $revisores = Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Revisor%'))
            ->withCount(['asignacionesComoRevisor as asignados_activos' => function ($q): void {
                $q->where('Estado_Asignacion', 'Activo');
            }])
            ->get();

        return view('portal.supervisor-dashboard', [
            'metricas' => $metricas,
            'expedientesPorAsignar' => $expedientesPorAsignar,
            'expedientesSupervision' => $expedientesSupervision,
            'revisores' => $revisores,
        ]);
    }
}
