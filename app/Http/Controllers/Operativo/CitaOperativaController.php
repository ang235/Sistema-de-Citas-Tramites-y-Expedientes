<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Cita;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CitaOperativaController extends Controller
{
    public function index(Request $request): View
    {
        $fecha = $request->query('fecha', today()->toDateString());
        $estado = $request->query('estado');

        $citas = Cita::query()
            ->whereDate('Fecha', $fecha)
            ->when($estado, function ($query, $st): void {
                $query->where('Estado_Citas', $st);
            })
            ->with(['usuario', 'solicitud.folio', 'solicitud.versionTramite.tipoTramite'])
            ->orderBy('Hora', 'asc')
            ->paginate(20);

        return view('portal.citas', [
            'citas' => $citas,
            'fechaFiltro' => $fecha,
            'estadoFiltro' => $estado,
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

