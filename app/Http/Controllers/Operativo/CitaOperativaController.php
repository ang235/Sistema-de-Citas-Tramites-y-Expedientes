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
        if (! $currentUser || ! ($currentUser->esSupervisor() || $currentUser->esAdministrador())) {
            if ($currentUser?->esRevisor()) {
                return redirect()->route('portal.revisor-dashboard');
            }

            return redirect()->route('expedientes.index');
        }

        // Cancelar automáticamente citas vencidas
        $citaService->cancelarCitasVencidas();

        $fecha = $request->query('fecha');
        $estado = $request->query('estado');

        $query = Cita::query()
            ->when($fecha && $fecha !== 'todas', function ($q) use ($fecha): void {
                $q->whereDate('Fecha', $fecha);
            })
            ->when($estado, function ($q, $st): void {
                $q->where('Estado_Citas', $st);
            })
            ->with(['usuario', 'solicitud.folio', 'solicitud.versionTramite.tipoTramite'])
            ->orderBy('Fecha', 'desc')
            ->orderBy('Hora', 'desc');

        $citas = $query->paginate(20)->withQueryString();

        $kpiQuery = Cita::query()
            ->when($fecha && $fecha !== 'todas', function ($q) use ($fecha): void {
                $q->whereDate('Fecha', $fecha);
            });

        return view('portal.citas', [
            'citas' => $citas,
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
        $validated = $request->validate([
            'estado_citas' => ['required', 'string', 'in:Programada,Atendida,Cancelada,No asistió'],
        ]);

        /** @var Cita $cita */
        $cita = Cita::findOrFail($idCita);
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
