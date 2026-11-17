<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Models\AsignacionRevisor;
use App\Models\Documento;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RevisorDashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        /** @var Usuario|null $usuario */
        $usuario = Auth::user();

        // Esta ventana es exclusiva del Revisor Técnico
        if (! $usuario || ! ($usuario->esRevisor() || $usuario->esAdministrador())) {
            if ($usuario?->esSupervisor()) {
                return redirect()->route('portal.supervisor-dashboard');
            }

            return redirect()->route('expedientes.index');
        }

        $idRevisor = (int) Auth::id();

        // Métricas exclusivas del Revisor Técnico
        $metricas = [
            'asignados' => AsignacionRevisor::where('ID_Revisor', $idRevisor)
                ->where(fn ($q) => $q->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion'))
                ->count(),
            'en_revision' => Solicitud::whereHas('asignaciones', function ($q) use ($idRevisor): void {
                $q->where('ID_Revisor', $idRevisor)
                    ->where(fn ($sub) => $sub->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion'));
            })->where('Estado_Solicitud', 'En_Revision')->count(),
            'con_observaciones' => Solicitud::whereHas('asignaciones', function ($q) use ($idRevisor): void {
                $q->where('ID_Revisor', $idRevisor)
                    ->where(fn ($sub) => $sub->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion'));
            })->where('Estado_Solicitud', 'Observado')->count(),
            'dictaminados' => Solicitud::whereHas('asignaciones', function ($q) use ($idRevisor): void {
                $q->where('ID_Revisor', $idRevisor);
            })->whereIn('Estado_Solicitud', ['Aprobado', 'Rechazado'])->count(),
            'documentos_pendientes' => Documento::whereHas('solicitud.asignaciones', function ($q) use ($idRevisor): void {
                $q->where('ID_Revisor', $idRevisor)
                    ->where(fn ($sub) => $sub->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion'));
            })->where('Estado_Doc', 'Pendiente')->count(),
        ];

        // Expedientes que el supervisor le asignó a este revisor
        $expedientesAsignados = Solicitud::whereHas('asignaciones', function ($q) use ($idRevisor): void {
            $q->where('ID_Revisor', $idRevisor)
                ->where(fn ($sub) => $sub->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion'));
        })
            ->with([
                'solicitante',
                'versionTramite.tipoTramite',
                'folio',
                'documentos.requisito',
                'asignaciones.revisiones',
            ])
            ->latest('ID_Solicitud')
            ->paginate(10);

        return view('portal.revisor-dashboard', [
            'metricas' => $metricas,
            'expedientesAsignados' => $expedientesAsignados,
        ]);
    }
}
