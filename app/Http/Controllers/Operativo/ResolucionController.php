<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Http\Middleware\RedirigirLocalhostAIp;
use App\Http\Requests\Operativo\EmitirResolucionRequest;
use App\Models\Bitacora;
use App\Models\Folio;
use App\Models\Resolucion;
use App\Models\Solicitud;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

class ResolucionController extends Controller
{
    public function store(EmitirResolucionRequest $request, int $idSolicitud): RedirectResponse
    {
        /** @var Usuario|null $user */
        $user = Auth::user();
        abort_if(! $user, 401);

        if (! $user->esSupervisor() && ! $user->esAdministrador()) {
            abort(403, 'Solo un usuario con rol de Supervisor o Administrador tiene facultades para emitir resoluciones formales.');
        }

        /** @var Solicitud $solicitud */
        $solicitud = Solicitud::with(['folio', 'resoluciones'])->findOrFail($idSolicitud);
        $idSupervisor = (int) $user->ID_Usuarios;

        if ($solicitud->resoluciones->isNotEmpty()) {
            return back()->with('error', 'El expediente ya cuenta con resolución formal emitida y no puede ser modificado.');
        }

        DB::transaction(function () use ($request, $solicitud, $idSupervisor): void {
            $tipoResolucion = $request->validated('tipo_resolucion');
            $comentarios = $request->validated('comentarios');

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
            $detalleBitacora = "Expediente {$folio} (#{$solicitud->ID_Solicitud}) concluido formalmente con Resolución {$tipoResolucion} (RES-{$resolucion->ID_Resolucion}).";
            if (! empty($comentarios)) {
                $detalleBitacora .= " Fundamentación: {$comentarios}";
            }

            Bitacora::create([
                'ID_Usuarios' => $idSupervisor,
                'Accion' => 'Expediente Concluido: Resolución Formal Emitida',
                'Descripcion' => $detalleBitacora,
                'Fecha_Creacion' => now(),
            ]);
        });

        return back()->with('success', 'Resolución formal emitida y registrada en el expediente.');
    }

    public function descargarPdf(int $idSolicitud): PdfBuilder
    {
        /** @var Solicitud $solicitud */
        $solicitud = Solicitud::with([
            'solicitante',
            'versionTramite.tipoTramite',
            'folio',
            'resoluciones.supervisor',
            'asignaciones.revisor',
        ])->findOrFail($idSolicitud);

        abort_if($solicitud->resoluciones->isEmpty(), 404, 'Este expediente no cuenta con resolución emitida aún.');

        /** @var Usuario|null $user */
        $user = Auth::user();
        if ($user && $user->esCiudadano() && (int) $solicitud->ID_Solicitante !== (int) $user->ID_Usuarios) {
            abort(403, 'No tiene autorización para consultar la resolución de este expediente.');
        }

        $resolucion = $solicitud->resoluciones->last();
        $folio = $solicitud->folio?->Codigo_Folio ?? "EXP-{$idSolicitud}";
        $qrHost = request()->getHost();
        $qrUrl = route('expedientes.show', ['codigoFolio' => $folio]);
        if (($qrHost === 'localhost' || $qrHost === '127.0.0.1') && $lanIp = RedirigirLocalhostAIp::obtenerIpRedActiva()) {
            $qrUrl = preg_replace('#^https?://(localhost|127\.0\.0\.1)#', 'http://'.$lanIp, $qrUrl);
        }

        return Pdf::view('pdf.resolucion', [
            'solicitud' => $solicitud,
            'resolucion' => $resolucion,
            'folio' => $folio,
            'qrUrl' => $qrUrl,
        ])
            ->format('a4')
            ->name("SETC-Resolucion-{$folio}.pdf");
    }

    /**
     * Permite la descarga pública de la resolución a través del código de folio oficial.
     */
    public function descargarPdfPorFolio(string $codigoFolio): PdfBuilder
    {
        $folio = Folio::where('Codigo_Folio', $codigoFolio)->firstOrFail();

        return $this->descargarPdf((int) $folio->ID_Solicitud);
    }
}
