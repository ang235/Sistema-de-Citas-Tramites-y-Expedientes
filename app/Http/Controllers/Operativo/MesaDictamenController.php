<?php

declare(strict_types=1);

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operativo\StoreRevisionRequest;
use App\Models\AsignacionRevisor;
use App\Models\Bitacora;
use App\Models\Documento;
use App\Models\Observacion;
use App\Models\Revision;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MesaDictamenController extends Controller
{
    public function index(Request $request): View
    {
        $idRevisor = (int) Auth::id();

        $solicitudes = Solicitud::whereHas('asignaciones', function ($q) use ($idRevisor): void {
            $q->where('ID_Revisor', $idRevisor);
        })
            ->with([
                'solicitante',
                'versionTramite.tipoTramite',
                'folio',
                'asignaciones.revisiones',
            ])
            ->latest('ID_Solicitud')
            ->paginate(15);

        return view('portal.mesa-dictamen', [
            'solicitudes' => $solicitudes,
        ]);
    }

    public function show(int $idSolicitud): View
    {
        /** @var Solicitud $solicitud */
        $solicitud = Solicitud::with([
            'solicitante',
            'versionTramite.tipoTramite',
            'versionTramite.requisitos',
            'folio',
            'datosSolicitud',
            'documentos.requisito',
            'documentos.versiones',
            'asignaciones.revisor',
            'asignaciones.revisiones.observaciones.respuestas',
            'resoluciones',
        ])->findOrFail($idSolicitud);

        $asignacionActiva = $solicitud->asignaciones->where('ID_Revisor', Auth::id())->last()
            ?? $solicitud->asignaciones->last();

        return view('portal.evaluacion', [
            'solicitud' => $solicitud,
            'asignacionActiva' => $asignacionActiva,
        ]);
    }

    public function evaluarDocumento(Request $request, int $idDocumento): RedirectResponse
    {
        $validated = $request->validate([
            'estado_doc' => ['required', 'string', 'in:Aprobado,Observado,Rechazado,Pendiente'],
        ]);

        /** @var Documento $documento */
        $documento = Documento::findOrFail($idDocumento);
        $documento->update(['Estado_Doc' => $validated['estado_doc']]);

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Evaluación de documento',
            'Descripcion' => "Documento #{$idDocumento} calificado como: {$validated['estado_doc']}.",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', "Estado del documento actualizado a {$validated['estado_doc']}.");
    }

    public function registrarRevision(StoreRevisionRequest $request, int $idAsignacion): RedirectResponse
    {
        /** @var AsignacionRevisor $asignacion */
        $asignacion = AsignacionRevisor::findOrFail($idAsignacion);

        DB::transaction(function () use ($request, $asignacion): void {
            $revision = Revision::create([
                'ID_Asignacion' => $asignacion->ID_Asignacion,
                'Resultado' => $request->validated('resultado'),
                'Fecha_Revision' => now(),
            ]);

            $observaciones = $request->validated('observaciones', []);
            if (! empty($observaciones)) {
                foreach ($observaciones as $obs) {
                    Observacion::create([
                        'ID_Revision' => $revision->ID_Revision,
                        'Detalles' => $obs['detalles'],
                        'Estado_Obs' => 'Abierta',
                    ]);
                }
            }

            Bitacora::create([
                'ID_Usuarios' => (int) Auth::id(),
                'Accion' => 'Dictamen de revisión registrado',
                'Descripcion' => "Revisión #{$revision->ID_Revision} concluida con resultado: {$revision->Resultado}.",
                'Fecha_Creacion' => now(),
            ]);
        });

        return redirect()->route('portal.mesa-dictamen')
            ->with('success', 'Dictamen técnico registrado exitosamente.');
    }
}

