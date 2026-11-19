<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BitacoraController extends Controller
{
    public function index(Request $request): View|StreamedResponse
    {
        $idSolicitante = $request->query('id_usuario');
        $tramiteFiltro = $request->query('accion') ?? $request->query('q');
        $fecha = $request->query('fecha');
        $estadoFiltro = $request->query('estado');

        $query = Solicitud::with([
            'folio',
            'solicitante',
            'versionTramite.tipoTramite',
            'asignaciones.revisor',
            'citas' => fn ($q) => $q->orderBy('Fecha')->orderBy('Hora'),
            'documentos.requisito',
            'documentos.versionesDoc',
        ])
            ->when($idSolicitante, fn ($q, $uid) => $q->where('ID_Solicitante', $uid))
            ->when($tramiteFiltro, function ($q, $term): void {
                $q->whereHas('versionTramite.tipoTramite', fn ($s) => $s->where('Nombre', 'like', "%{$term}%"));
            })
            ->when($fecha, fn ($q, $f) => $q->whereDate('Fecha_Creacion_Solicitud', $f))
            ->when($estadoFiltro, fn ($q, $e) => $q->where('Estado_Solicitud', $e))
            ->orderBy('Fecha_Creacion_Solicitud', 'desc');

        // Exportar a CSV si se solicita
        if ($request->query('export') === 'csv') {
            $registros = $query->get();
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="bitacora_expedientes_'.now()->format('Y-m-d_His').'.csv"',
            ];

            return response()->stream(function () use ($registros): void {
                $handle = fopen('php://output', 'w');
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['Folio', 'Ciudadano', 'Email', 'Trámite', 'Estado Solicitud', 'Fecha Solicitud', 'Fecha Cita', 'Hora Cita', 'Estado Cita', 'Revisor Asignado']);

                foreach ($registros as $sol) {
                    $cita = $sol->citas->first();
                    $revisor = $sol->asignaciones->first()?->revisor ?? $cita?->revisor ?? null;

                    fputcsv($handle, [
                        $sol->folio?->Codigo_Folio ?? 'Sin folio',
                        $sol->solicitante?->Nombre ?? 'N/A',
                        $sol->solicitante?->Email ?? 'N/A',
                        $sol->versionTramite?->tipoTramite?->Nombre ?? 'N/A',
                        $sol->Estado_Solicitud,
                        $sol->Fecha_Creacion_Solicitud?->format('Y-m-d'),
                        $cita?->Fecha ? Carbon::parse($cita->Fecha)->format('Y-m-d') : 'Sin cita',
                        $cita?->Hora ? substr((string) $cita->Hora, 0, 5) : '--',
                        $cita?->Estado_Citas ?? '--',
                        $revisor?->Nombre ?? 'Sin asignar',
                    ]);
                }
                fclose($handle);
            }, 200, $headers);
        }

        $solicitudes = $query->paginate(20)->appends($request->query());

        $ciudadanos = Usuario::whereHas('solicitudes')
            ->select('ID_Usuarios', 'Nombre', 'Email')
            ->orderBy('Nombre')
            ->get();

        $kpis = [
            'total_expedientes' => Solicitud::count(),
            'expedientes_hoy' => Solicitud::whereDate('Fecha_Creacion_Solicitud', today())->count(),
            'con_cita' => Solicitud::whereHas('citas')->count(),
            'concluidos' => Solicitud::whereIn('Estado_Solicitud', ['Aprobado', 'Concluido'])->count(),
        ];

        return view('portal.auditoria', [
            'solicitudes' => $solicitudes,
            'ciudadanos' => $ciudadanos,
            'idUsuarioFiltro' => $idSolicitante,
            'accionFiltro' => $tramiteFiltro,
            'fechaFiltro' => $fecha,
            'estadoFiltro' => $estadoFiltro,
            'kpis' => $kpis,
        ]);
    }
}
