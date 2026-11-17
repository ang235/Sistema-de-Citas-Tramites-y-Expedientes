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
        $solicitud = Solicitud::with(['folio', 'resoluciones'])->findOrFail($idSolicitud);
        $idSupervisor = (int) Auth::id();

        if ($solicitud->resoluciones->isNotEmpty()) {
            return back()->with('error', 'El expediente ya cuenta con resolución formal emitida y no puede ser modificado.');
        }

        DB::transaction(function () use ($request, $solicitud, $idSupervisor): void {
            $tipoResolucion = $request->validated('tipo_resolucion');

            $resolucion = Resolucion::create([
                'ID_Solicitud' => $solicitud->ID_Solicitud,
                'ID_Supervisor' => $idSupervisor,
                'Fecha_Emision_Resolucion' => now(),
                'Tipo_Resolucion' => $tipoResolucion,
            ]);

            // Sincronizar estado final del expediente
            if ($tipoResolucion === 'Favorable') {
                $solicitud->update(['Estado_Solicitud' => 'Aprobado']);
            } elseif (in_array($tipoResolucion, ['Desfavorable', 'Inadmisible', 'Cancelada'])) {
                $solicitud->update(['Estado_Solicitud' => 'Rechazado']);
            }

            $folio = $solicitud->folio?->Codigo_Folio ?? "EXP-{$solicitud->ID_Solicitud}";

            Bitacora::create([
                'ID_Usuarios' => $idSupervisor,
                'Accion' => 'Expediente Concluido: Resolución Formal Emitida',
                'Descripcion' => "Expediente {$folio} (#{$solicitud->ID_Solicitud}) concluido formalmente con Resolución {$tipoResolucion} (RES-{$resolucion->ID_Resolucion}).",
                'Fecha_Creacion' => now(),
            ]);
        });

        return back()->with('success', 'Resolución formal emitida y registrada en el expediente.');
    }
}
