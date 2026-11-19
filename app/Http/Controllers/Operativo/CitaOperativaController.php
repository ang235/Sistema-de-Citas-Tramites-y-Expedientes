<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Cita;
use App\Models\Usuario;
use App\Services\CitaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CitaOperativaController extends Controller
{
    public function index(Request $request, CitaService $citaService): View|RedirectResponse
    {
        /** @var Usuario|null $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser || ! ($currentUser->esSupervisor() || $currentUser->esAdministrador() || $currentUser->esRevisor())) {
            return redirect()->route('expedientes.index');
        }

        // Cancelar automáticamente citas vencidas
        $citaService->cancelarCitasVencidas();

        $fecha = $request->query('fecha');
        $estado = $request->query('estado');
        $idRevisorFiltro = $request->query('revisor');

        $esSoloRevisor = $currentUser->esRevisor() && ! $currentUser->esSupervisor() && ! $currentUser->esAdministrador();
        if ($esSoloRevisor) {
            $idRevisorFiltro = (string) $currentUser->ID_Usuarios;
        }

        $query = Cita::query()
            ->when($fecha && $fecha !== 'todas', function ($q) use ($fecha): void {
                $q->whereDate('Fecha', $fecha);
            })
            ->when($estado, function ($q, $st): void {
                $q->where('Estado_Citas', $st);
            })
            ->when($idRevisorFiltro, function ($q, $revId): void {
                $q->where(function ($sub) use ($revId): void {
                    $sub->where('ID_Revisor', $revId)
                        ->orWhere(function ($legacy) use ($revId): void {
                            $legacy->whereNull('ID_Revisor')
                                ->whereHas('solicitud.asignaciones', function ($asig) use ($revId): void {
                                    $asig->where('ID_Revisor', $revId);
                                });
                        });
                });
            })
            ->with(['usuario', 'revisor', 'solicitud.folio', 'solicitud.versionTramite.tipoTramite', 'solicitud.documentos.requisito'])
            ->orderBy('Fecha', 'desc')
            ->orderBy('Hora', 'desc');

        $citas = $query->paginate(20)->withQueryString();

        $revisores = Usuario::whereHas('roles', function ($q): void {
            $q->where('Nombre', 'like', '%Revisor%')
                ->orWhere('Nombre', 'like', '%Supervisor%');
        })->where('Activo', true)->orderBy('Nombre')->get();

        $kpiQuery = Cita::query()
            ->when($fecha && $fecha !== 'todas', function ($q) use ($fecha): void {
                $q->whereDate('Fecha', $fecha);
            })
            ->when($idRevisorFiltro, function ($q, $revId): void {
                $q->where(function ($sub) use ($revId): void {
                    $sub->where('ID_Revisor', $revId)
                        ->orWhere(function ($legacy) use ($revId): void {
                            $legacy->whereNull('ID_Revisor')
                                ->whereHas('solicitud.asignaciones', function ($asig) use ($revId): void {
                                    $asig->where('ID_Revisor', $revId);
                                });
                        });
                });
            });

        return view('portal.citas', [
            'citas' => $citas,
            'revisores' => $revisores,
            'revisorFiltro' => $idRevisorFiltro,
            'esSoloRevisor' => $esSoloRevisor,
            'fechaFiltro' => $fecha,
            'estadoFiltro' => $estado,
            'kpiTotal' => (clone $kpiQuery)->count(),
            'kpiProgramadas' => (clone $kpiQuery)->where('Estado_Citas', 'Programada')->count(),
            'kpiAtendidas' => (clone $kpiQuery)->where('Estado_Citas', 'Atendida')->count(),
            'kpiCanceladas' => (clone $kpiQuery)->whereIn('Estado_Citas', ['Cancelada', 'No asistió'])->count(),
        ]);
    }

    public function actualizarEstado(Request $request, int $idCita): RedirectResponse
    {
        /** @var Usuario|null $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser) {
            abort(401);
        }

        // Ni el supervisor ni el administrador modifican citas operativas directamente (función exclusiva del Revisor Técnico asignado)
        if ($currentUser->esSupervisor() || $currentUser->esAdministrador()) {
            abort(403, 'Los supervisores y administradores no tienen permisos para modificar citas en ventanilla. Esta acción es de solo lectura y exclusiva del Revisor Técnico asignado.');
        }

        if (! ($currentUser->esAdministrador() || $currentUser->esRevisor())) {
            abort(403);
        }

        $validated = $request->validate([
            'estado_citas' => ['required', 'string', 'in:Programada,Atendida,Cancelada,No asistió'],
        ]);

        /** @var Cita $cita */
        $cita = Cita::findOrFail($idCita);

        if ($currentUser->esRevisor() && ! $currentUser->esAdministrador()) {
            $esRevisorAsignado = $cita->ID_Revisor === $currentUser->ID_Usuarios
                || ($cita->ID_Revisor === null && $cita->solicitud?->asignaciones()->where('ID_Revisor', $currentUser->ID_Usuarios)->exists());

            if (! $esRevisorAsignado) {
                abort(403, 'No tiene autorización para gestionar citas asignadas a otro revisor.');
            }
        }

        $cita->update(['Estado_Citas' => $validated['estado_citas']]);

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Atención de cita en ventanilla',
            'Descripcion' => "Cita #{$idCita} marcada con estado: {$validated['estado_citas']}.",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', "Estado de la cita actualizado a {$validated['estado_citas']}.");
    }
}
