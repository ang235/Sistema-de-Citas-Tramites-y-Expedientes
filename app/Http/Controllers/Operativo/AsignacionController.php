<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operativo\AsignarExpedienteRequest;
use App\Models\AsignacionRevisor;
use App\Models\Bitacora;
use App\Models\Revision;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AsignacionController extends Controller
{
    public function index(Request $request): View
    {
        $pendientes = Solicitud::doesntHave('asignaciones')
            ->with(['solicitante', 'versionTramite.tipoTramite', 'folio', 'documentos'])
            ->latest('ID_Solicitud')
            ->paginate(15, ['*'], 'pendientes_page');

        $revisores = Usuario::whereHas('roles', function ($q): void {
            $q->whereIn('Nombre', ['Revisor', 'Revisor L1', 'Revisor L2']);
        })->get();

        return view('portal.matriz-cargas', [
            'pendientes' => $pendientes,
            'revisores' => $revisores,
        ]);
    }

    public function store(AsignarExpedienteRequest $request): RedirectResponse
    {
        $idAsignador = (int) Auth::id();

        DB::transaction(function () use ($request, $idAsignador): void {
            $asignacion = AsignacionRevisor::create([
                'ID_Solicitud' => $request->validated('id_solicitud'),
                'ID_Revisor' => $request->validated('id_revisor'),
                'ID_Asignador' => $idAsignador,
                'Fecha_Asignacion' => now(),
            ]);

            // Iniciar ciclo de revisión
            Revision::create([
                'ID_Asignacion' => $asignacion->ID_Asignacion,
                'Resultado' => 'En Revisión',
                'Fecha_Revision' => now(),
            ]);

            Bitacora::create([
                'ID_Usuarios' => $idAsignador,
                'Accion' => 'Asignación de expediente',
                'Descripcion' => "Solicitud #{$request->validated('id_solicitud')} asignada al revisor #{$request->validated('id_revisor')}.",
                'Fecha_Creacion' => now(),
            ]);
        });

        return back()->with('success', 'Expediente asignado al revisor técnico exitosamente.');
    }
}

