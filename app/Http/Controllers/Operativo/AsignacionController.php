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
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AsignacionController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        /** @var Usuario|null $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser || ! $currentUser->esSupervisor()) {
            if ($currentUser?->esAdministrador()) {
                return redirect()->route('portal.dashboard');
            }

            if ($currentUser?->esRevisor()) {
                return redirect()->route('portal.revisor-dashboard');
            }

            return redirect()->route('expedientes.index');
        }

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
            ->withCount([
                'asignacionesComoRevisor as asignaciones_count',
                'asignacionesComoRevisor as asignaciones_activas_count' => function ($q): void {
                    $q->where(function ($sub): void {
                        $sub->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion');
                    })->whereHas('solicitud', function ($sub): void {
                        $sub->whereNotIn('Estado_Solicitud', ['Aprobado', 'Rechazado'])
                            ->doesntHave('resoluciones');
                    });
                },
                'asignacionesComoRevisor as asignaciones_resueltas_count' => function ($q): void {
                    $q->where(function ($sub): void {
                        $sub->whereHas('solicitud', function ($s): void {
                            $s->whereIn('Estado_Solicitud', ['Aprobado', 'Rechazado'])
                                ->orWhereHas('resoluciones');
                        })->orWhereHas('revisiones', function ($r): void {
                            $r->whereIn('Resultado', ['Aprobado', 'Rechazado']);
                        });
                    });
                },
            ])
            ->get();

        // 1. Revisores y Supervisores reales
        $totalRevisores = Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Revisor%'))->count();
        $revisoresConCarga = Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Revisor%'))
            ->whereHas('asignacionesComoRevisor', function ($q): void {
                $q->where(function ($sub): void {
                    $sub->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion');
                })->whereHas('solicitud', function ($sub): void {
                    $sub->whereNotIn('Estado_Solicitud', ['Aprobado', 'Rechazado'])
                        ->doesntHave('resoluciones');
                });
            })
            ->count();
        $totalSupervisores = Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Supervisor%'))->count();

        // 2. Expedientes en Cola y Críticos (>3 días sin asignar)
        $enCola = Solicitud::doesntHave('asignaciones')->count();
        $criticos = Solicitud::doesntHave('asignaciones')
            ->where('Fecha_Creacion_Solicitud', '<=', now()->subDays(3))
            ->count();

        // 3. Tiempos promedio de dictamen a partir de revisiones formalizadas
        $revisionesConcluidas = Revision::whereIn('Resultado', ['Aprobado', 'Rechazado', 'Observado'])
            ->whereNotNull('Fecha_Revision')
            ->with('asignacion')
            ->get();

        $minutosTotales = 0;
        $conteoRevisiones = 0;
        $enPlazoSLA = 0;

        foreach ($revisionesConcluidas as $rev) {
            if ($rev->asignacion && $rev->asignacion->Fecha_Asignacion) {
                $inicio = Carbon::parse($rev->asignacion->Fecha_Asignacion);
                $fin = Carbon::parse($rev->Fecha_Revision);
                $diff = max(1, $fin->diffInMinutes($inicio));
                $minutosTotales += $diff;
                $conteoRevisiones++;

                if ($fin->diffInDays($inicio) <= 5) {
                    $enPlazoSLA++;
                }
            }
        }

        $tiempoPromedioTexto = 'Sin registros';
        if ($conteoRevisiones > 0) {
            $promMinutos = (int) round($minutosTotales / $conteoRevisiones);
            $horas = intdiv($promMinutos, 60);
            $mins = $promMinutos % 60;
            $tiempoPromedioTexto = ($horas > 0 ? "{$horas}h " : '')."{$mins}m";
        }

        $cumplimientoSLA = $conteoRevisiones > 0
            ? round(($enPlazoSLA / $conteoRevisiones) * 100, 1)
            : 100.0;

        // 4. Capacidad Operativa Real (solo expedientes en curso / activos, excluyendo resueltos)
        $capacidadNominal = $totalRevisores * 10;
        $enCurso = AsignacionRevisor::where(function ($q): void {
            $q->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion');
        })->whereHas('solicitud', function ($q): void {
            $q->whereNotIn('Estado_Solicitud', ['Aprobado', 'Rechazado'])
                ->doesntHave('resoluciones');
        })->count();

        $cuposDisponibles = max(0, $capacidadNominal - $enCurso);
        $capacidadPorcentaje = $capacidadNominal > 0
            ? min(100, (int) round(($enCurso / $capacidadNominal) * 100))
            : 0;

        $kpis = [
            'revisores_totales' => $totalRevisores,
            'revisores_con_carga' => $revisoresConCarga,
            'supervisores_totales' => $totalSupervisores,
            'en_cola' => $enCola,
            'criticos' => $criticos,
            'tiempo_promedio' => $tiempoPromedioTexto,
            'total_dictaminados' => $conteoRevisiones,
            'cumplimiento_sla' => $cumplimientoSLA,
        ];

        return view('portal.matriz-cargas', [
            'pendientes' => $pendientes,
            'revisores' => $revisores,
            'enCurso' => $enCurso,
            'capacidadNominal' => $capacidadNominal,
            'cuposDisponibles' => $cuposDisponibles,
            'capacidadPorcentaje' => $capacidadPorcentaje,
            'capacidad' => $capacidadPorcentaje,
            'kpis' => $kpis,
            'buscar' => $buscar,
            'nivel' => $nivel,
        ]);
    }

    public function store(AsignarExpedienteRequest $request): RedirectResponse
    {
        $idAsignador = (int) Auth::id();

        DB::transaction(function () use ($request, $idAsignador): void {
            $idSolicitud = (int) $request->validated('id_solicitud');
            $idRevisor = (int) $request->validated('id_revisor');

            // Marcar asignaciones previas como reasignadas
            AsignacionRevisor::where('ID_Solicitud', $idSolicitud)
                ->where('Estado_Asignacion', 'Activo')
                ->update(['Estado_Asignacion' => 'Reasignado']);

            $asignacion = AsignacionRevisor::create([
                'ID_Solicitud' => $idSolicitud,
                'ID_Revisor' => $idRevisor,
                'ID_Asignador' => $idAsignador,
                'Estado_Asignacion' => 'Activo',
                'Fecha_Asignacion' => now(),
            ]);

            // Actualizar la solicitud al estado En_Revision
            $solicitud = Solicitud::with('folio')->find($idSolicitud);
            $solicitud?->update(['Estado_Solicitud' => 'En_Revision']);

            $revisor = Usuario::find($idRevisor);

            // Iniciar ciclo de revisión
            Revision::create([
                'ID_Asignacion' => $asignacion->ID_Asignacion,
                'Resultado' => 'En Revisión',
                'Fecha_Revision' => now(),
            ]);

            $codigoFolio = $solicitud?->folio?->Codigo_Folio ?? "EXP-{$idSolicitud}";
            Bitacora::create([
                'ID_Usuarios' => $idAsignador,
                'Accion' => 'Asignación de expediente a revisor',
                'Descripcion' => "Expediente {$codigoFolio} asignado formalmente al revisor técnico {$revisor?->Nombre} (#{$idRevisor}) para examen documental.",
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
            ->withCount(['asignacionesComoRevisor as asignaciones_activas_count' => function ($q): void {
                $q->where(function ($sub): void {
                    $sub->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion');
                })->whereHas('solicitud', function ($sub): void {
                    $sub->whereNotIn('Estado_Solicitud', ['Aprobado', 'Rechazado'])
                        ->doesntHave('resoluciones');
                });
            }])
            ->orderBy('asignaciones_activas_count')
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
                    'Estado_Asignacion' => 'Activo',
                    'Fecha_Asignacion' => now(),
                ]);

                $solicitud->update(['Estado_Solicitud' => 'En_Revision']);

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
                'Accion' => 'Distribución automática de expedientes',
                'Descripcion' => "Se distribuyeron automáticamente {$asignados} expedientes entre los revisores técnicos activos.",
                'Fecha_Creacion' => now(),
            ]);
        });

        return back()->with('success', "Asignación inteligente completada: {$asignados} expedientes distribuidos.");
    }
}
