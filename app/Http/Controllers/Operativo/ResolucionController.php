<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operativo\EmitirResolucionRequest;
use App\Models\Bitacora;
use App\Models\Resolucion;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResolucionController extends Controller
{
    public function store(EmitirResolucionRequest $request, int $idSolicitud): RedirectResponse
    {
        /** @var Solicitud $solicitud */
        $solicitud = Solicitud::findOrFail($idSolicitud);
        $idSupervisor = (int) Auth::id();

        DB::transaction(function () use ($request, $solicitud, $idSupervisor): void {
            $resolucion = Resolucion::create([
                'ID_Solicitud' => $solicitud->ID_Solicitud,
                'ID_Supervisor' => $idSupervisor,
                'Fecha_Emision_Resolucion' => now(),
                'Tipo_Resolucion' => $request->validated('tipo_resolucion'),
            ]);

            Bitacora::create([
                'ID_Usuarios' => $idSupervisor,
                'Accion' => 'Emisión de resolución formal',
                'Descripcion' => "Resolución #{$resolucion->ID_Resolucion} emitida para solicitud #{$solicitud->ID_Solicitud} con tipo: {$resolucion->Tipo_Resolucion}.",
                'Fecha_Creacion' => now(),
            ]);
        });

        return back()->with('success', 'Resolución formal emitida y registrada en el expediente.');
    }
}

