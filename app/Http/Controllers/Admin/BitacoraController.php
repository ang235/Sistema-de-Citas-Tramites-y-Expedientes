<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BitacoraController extends Controller
{
    public function index(Request $request): View|StreamedResponse
    {
        $idUsuario = $request->query('id_usuario');
        $accion = $request->query('accion') ?? $request->query('q');
        $fecha = $request->query('fecha');

        $query = Bitacora::query()
            ->when($idUsuario, function ($q, $uid): void {
                $q->where('ID_Usuarios', $uid);
            })
            ->when($accion, function ($q, $term): void {
                $q->where(function ($sub) use ($term): void {
                    $sub->where('Accion', 'like', "%{$term}%")
                        ->orWhere('Descripcion', 'like', "%{$term}%");
                });
            })
            ->when($fecha, function ($q, $f): void {
                $q->whereDate('Fecha_Creacion', $f);
            })
            ->with('usuario')
            ->orderBy('ID_Bitacora', 'desc');

        // Exportar a CSV si se solicita
        if ($request->query('export') === 'csv') {
            $registros = $query->get();
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="bitacora_setc_'.now()->format('Y-m-d_His').'.csv"',
            ];

            return response()->stream(function () use ($registros): void {
                $handle = fopen('php://output', 'w');
                // UTF-8 BOM para Excel
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, ['ID Bitácora', 'Fecha y Hora', 'ID Usuario', 'Funcionario', 'Correo', 'Acción', 'Descripción']);

                foreach ($registros as $row) {
                    fputcsv($handle, [
                        $row->ID_Bitacora,
                        $row->Fecha_Creacion ? $row->Fecha_Creacion->format('Y-m-d H:i:s') : '',
                        $row->ID_Usuarios,
                        $row->usuario?->Nombre ?? 'Sistema',
                        $row->usuario?->Email ?? '',
                        $row->Accion,
                        $row->Descripcion,
                    ]);
                }
                fclose($handle);
            }, 200, $headers);
        }

        $bitacoras = $query->paginate(25)->appends($request->query());

        $usuarios = Usuario::select('ID_Usuarios', 'Nombre', 'Email')->orderBy('Nombre')->get();

        $kpis = [
            'total_eventos' => Bitacora::count(),
            'eventos_hoy' => Bitacora::whereDate('Fecha_Creacion', today())->count(),
            'usuarios_activos' => Bitacora::distinct('ID_Usuarios')->count('ID_Usuarios'),
            'cambios_normativos' => Bitacora::where('Accion', 'like', '%trámite%')
                ->orWhere('Accion', 'like', '%versión%')
                ->orWhere('Accion', 'like', '%requisito%')
                ->count(),
            'alertas_discrepancia' => 0,
        ];

        return view('portal.auditoria', [
            'bitacoras' => $bitacoras,
            'usuarios' => $usuarios,
            'idUsuarioFiltro' => $idUsuario,
            'accionFiltro' => $accion,
            'fechaFiltro' => $fecha,
            'kpis' => $kpis,
            'totalEventos' => $kpis['total_eventos'],
            'cambiosNormativos' => $kpis['cambios_normativos'],
            'alertasDiscrepancia' => $kpis['alertas_discrepancia'],
        ]);
    }
}
