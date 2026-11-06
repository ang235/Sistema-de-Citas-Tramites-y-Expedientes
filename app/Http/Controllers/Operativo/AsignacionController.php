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
        $buscar = $request->query('buscar');
        $nivel = $request->query('nivel');

        $pendientes = Solicitud::doesntHave('asignaciones')
            ->with(['solicitante', 'versionTramite.tipoTramite', 'folio', 'documentos'])
            ->latest('ID_Solicitud')
            ->paginate(15, ['*'], 'pendientes_page');

        $revisores = Usuario::query()
            ->whereHas('roles', function ($q) use ($nivel): void {
                if ($nivel && $nivel !== 'Todos los niveles') {
                    $q->where('Nombre', 'like', "%{$nivel}%");
                } else {
                    $q->where('Nombre', 'like', '%Revisor%')
                      ->orWhere('Nombre', 'like', '%Supervisor%');
                }
            })
            ->when($buscar, function ($query, $term): void {
                $query->where(function ($q) use ($term): void {
                    $q->where('Nombre', 'like', "%{$term}%")
                      ->orWhere('Email', 'like', "%{$term}%");
                });
            })
            ->withCount('asignacionesComoRevisor')
            ->get();

        $enCurso = AsignacionRevisor::whereHas('revisiones', function ($q): void {
            $q->where('Resultado', 'En Revisión');
        })->count();

        return view('portal.matriz-cargas', [
            'pendientes' => $pendientes,
            'revisores' => $revisores,
            'enCurso' => $enCurso,
            'capacidad' => min(100, (int) round(($enCurso / max(1, 85)) * 100)),
            'buscar' => $buscar,
            'nivel' => $nivel,
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

    public function autoAsignar(): RedirectResponse
    {
        $idAsignador = (int) Auth::id();

        $pendientes = Solicitud::doesntHave('asignaciones')->get();
        if ($pendientes->isEmpty()) {
            return back()->with('info', 'No hay expedientes pendientes de asignación en cola.');
        }

        $revisores = Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Revisor%'))
            ->withCount('asignacionesComoRevisor')
            ->orderBy('asignaciones_como_revisor_count')
            ->get();

        if ($revisores->isEmpty()) {
            return back()->with('error', 'No existen revisores técnicos activos en el sistema.');
        }

        $asignados = 0;
        DB::transaction(function () use ($pendientes, $revisores, $idAsignador, &$asignados): void {
            $countRev = $revisores->count();
            $idx = 0;

            foreach ($pendientes as $solicitud) {
                $revisor = $revisores[$idx % $countRev];

                $asig = AsignacionRevisor::create([
                    'ID_Solicitud' => $solicitud->ID_Solicitud,
                    'ID_Revisor' => $revisor->ID_Usuarios,
                    'ID_Asignador' => $idAsignador,
                    'Fecha_Asignacion' => now(),
                ]);

                Revision::create([
                    'ID_Asignacion' => $asig->ID_Asignacion,
                    'Resultado' => 'En Revisión',
                    'Fecha_Revision' => now(),
                ]);

                $asignados++;
                $idx++;
            }

            Bitacora::create([
                'ID_Usuarios' => $idAsignador,
                'Accion' => 'Asignación inteligente y balanceo',
                'Descripcion' => "Se asignaron {$asignados} expedientes balanceados entre el equipo pericial.",
                'Fecha_Creacion' => now(),
            ]);
        });

        return back()->with('success', "Asignación inteligente completada: {$asignados} expedientes distribuidos.");
    }
}

