<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ciudadano\SolventarObservacionRequest;
use App\Models\Bitacora;
use App\Models\Folio;
use App\Models\Observacion;
use App\Models\Respuesta;
use App\Models\Solicitud;
use App\Models\VersionDoc;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExpedienteCiudadanoController extends Controller
{
    public function index(Request $request): View
    {
        $idUsuario = (int) Auth::id();
        $codigoFolio = $request->query('folio');

        $solicitudes = Solicitud::query()
            ->where('ID_Solicitante', $idUsuario)
            ->when($codigoFolio, function ($query, $folio): void {
                $query->whereHas('folio', function ($q) use ($folio): void {
                    $q->where('Codigo_Folio', 'like', "%{$folio}%");
                });
            })
            ->with(['folio', 'versionTramite.tipoTramite', 'documentos', 'resoluciones'])
            ->latest('ID_Solicitud')
            ->paginate(10);

        return view('public.expedientes.index', [
            'solicitudes' => $solicitudes,
            'busquedaFolio' => $codigoFolio,
        ]);
    }

    public function show(string $codigoFolio): View
    {
        /** @var Folio $folio */
        $folio = Folio::where('Codigo_Folio', $codigoFolio)->firstOrFail();

        /** @var Solicitud $solicitud */
        $solicitud = Solicitud::with([
            'solicitante',
            'versionTramite.tipoTramite',
            'versionTramite.requisitos',
            'folio',
            'datosSolicitud',
            'documentos.requisito',
            'documentos.versiones',
            'asignaciones.revisiones.observaciones.respuestas',
            'citas',
            'resoluciones.supervisor',
        ])->findOrFail($folio->ID_Solicitud);

        /** @var Usuario|null $user */
        $user = Auth::user();
        if ($user !== null && ! $user->esAdministrador() && ! $user->esRevisor() && ! $user->esSupervisor()) {
            abort_if($solicitud->ID_Solicitante !== $user->ID_Usuarios, 403, 'No tiene autorización para consultar este expediente.');
        }

        return view('public.expedientes.show', [
            'solicitud' => $solicitud,
            'folio' => $folio,
        ]);
    }

    public function solventarObservacion(SolventarObservacionRequest $request, int $idObservacion): RedirectResponse
    {
        /** @var Observacion $observacion */
        $observacion = Observacion::with(['revision.asignacion.solicitud.folio'])->findOrFail($idObservacion);
        $solicitud = $observacion->revision->asignacion->solicitud;

        // Validar propiedad del solicitante si está autenticado
        if (Auth::check() && $solicitud->ID_Solicitante !== Auth::id()) {
            abort(403, 'No tiene autorización para solventar este expediente.');
        }

        DB::transaction(function () use ($request, $observacion, $solicitud): void {
            $idVersionDoc = null;

            if ($request->hasFile('archivo')) {
                // Asociar preferentemente al documento que tenga estado Observado, Rechazado o Pendiente
                $doc = $solicitud->documentos->first(fn ($d) => in_array($d->Estado_Doc, ['Observado', 'Rechazado', 'Pendiente'], true))
                    ?? $solicitud->documentos->first();

                if ($doc !== null) {
                    $archivo = $request->file('archivo');
                    $ruta = $archivo->store("expedientes/{$solicitud->ID_Solicitud}");

                    $versionDoc = VersionDoc::create([
                        'ID_Documento' => $doc->ID_Documento,
                        'Fecha_Carga' => now(),
                        'Ruta_Archivo' => $ruta,
                        'Nombre_Original' => $archivo->getClientOriginalName(),
                    ]);
                    $idVersionDoc = $versionDoc->ID_Version_Doc;

                    $doc->update(['Estado_Doc' => 'Subsanado']);
                }
            }

            Respuesta::create([
                'ID_Observacion' => $observacion->ID_Observacion,
                'ID_Version_Doc' => $idVersionDoc,
                'Mensaje_Respuesta' => $request->validated('mensaje_respuesta'),
                'Fecha_Respuesta' => now(),
            ]);

            $observacion->update(['Estado_Obs' => 'Subsanada']);

            // Si no quedan más observaciones abiertas en la solicitud, regresa a estado 'En_Revision'
            $otrasObsAbiertas = Observacion::whereHas('revision.asignacion', fn ($q) => $q->where('ID_Solicitud', $solicitud->ID_Solicitud))
                ->where('Estado_Obs', 'Abierta')
                ->where('ID_Observacion', '!=', $observacion->ID_Observacion)
                ->exists();

            if (! $otrasObsAbiertas && $solicitud->Estado_Solicitud === 'Observado') {
                $solicitud->update(['Estado_Solicitud' => 'En_Revision']);
            }

            Bitacora::create([
                'ID_Usuarios' => (int) Auth::id(),
                'Accion' => 'Subsanación de observación',
                'Descripcion' => "Respuesta ingresada para la observación #{$observacion->ID_Observacion} en el expediente #{$solicitud->ID_Solicitud}.",
                'Fecha_Creacion' => now(),
            ]);
        });

        $codigo = $solicitud->folio?->Codigo_Folio ?? (string) $solicitud->ID_Solicitud;

        return redirect()->route('expedientes.show', ['codigoFolio' => $codigo])
            ->with('success', 'Observación solventada exitosamente.');
    }
}
