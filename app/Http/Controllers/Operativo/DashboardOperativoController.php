<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Revision;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardOperativoController extends Controller
{
    public function index(): View
    {
        $idUsuario = (int) Auth::id();

        $metricas = [
            'total_solicitudes' => Solicitud::count(),
            'pendientes_asignar' => Solicitud::doesntHave('asignaciones')->count(),
            'en_revision' => Revision::where('Resultado', 'En Revisión')->count(),
            'con_observaciones' => Revision::where('Resultado', 'Observado')->count(),
            'aprobadas' => Revision::where('Resultado', 'Aprobado')->count(),
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
}

