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
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();
        $idUsuario = (int) Auth::id();
        $search = $request->query('buscar');
        $idTipoTramite = $request->query('id_tipo_tramite');

        $esRevisor = $usuario->esRevisor();
        $esSupervisor = $usuario->esSupervisor();
        $esAdmin = $usuario->esAdministrador();

        $query = Solicitud::query()
            ->with([
                'solicitante',
                'versionTramite.tipoTramite',
                'folio',
                'documentos',
                'asignaciones.revisor',
                'asignaciones.revisiones',
            ]);

        // Si es revisor y no es supervisor ni admin, filtrar sus asignaciones (o las disponibles si no tiene)
        if ($esRevisor && ! $esSupervisor && ! $esAdmin) {
            $tieneAsignadas = AsignacionRevisor::where('ID_Revisor', $idUsuario)->exists();
            if ($tieneAsignadas) {
                $query->whereHas('asignaciones', function ($q) use ($idUsuario): void {
                    $q->where('ID_Revisor', $idUsuario);
                });
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->whereHas('folio', fn ($f) => $f->where('Codigo_Folio', 'like', "%{$search}%"))
                  ->orWhereHas('solicitante', fn ($s) => $s->where('Nombre', 'like', "%{$search}%")->orWhere('Email', 'like', "%{$search}%"))
                  ->orWhereHas('versionTramite.tipoTramite', fn ($t) => $t->where('Nombre', 'like', "%{$search}%"));
            });
        }

        if ($idTipoTramite) {
            $query->whereHas('versionTramite', fn ($v) => $v->where('ID_Tipo_Tramite', $idTipoTramite));
        }

        $solicitudes = $query->latest('ID_Solicitud')->paginate(15);

        // Métricas de sala
        $totalAsignados = $esRevisor && ! $esSupervisor ? AsignacionRevisor::where('ID_Revisor', $idUsuario)->count() : AsignacionRevisor::count();
        $revisadosHoy = Revision::whereDate('Fecha_Revision', today())->count();
        $enSubsanacion = Revision::where('Resultado', 'Observado')->count();

        $metricas = [
            'expedientes_asignados' => $totalAsignados,
            'revisados_hoy' => $revisadosHoy,
            'en_subsanacion' => $enSubsanacion,
            'prioritarios_sla' => Solicitud::doesntHave('asignaciones')->count(),
        ];

        $tiposTramite = \App\Models\TipoTramite::where('Estado_Tipo_Tr', 'Activo')->orderBy('Nombre')->get();

        return view('portal.mesa-dictamen', [
            'solicitudes' => $solicitudes,
            'metricas' => $metricas,
            'tiposTramite' => $tiposTramite,
            'buscar' => $search,
            'idTipoTramite' => $idTipoTramite,
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

        // Si es Revisor y la solicitud aún no tiene asignación, se autoasigna para permitir evaluar de inmediato
        if (! $asignacionActiva && Auth::check() && Auth::user()->esRevisor()) {
            $asignacionActiva = AsignacionRevisor::create([
                'ID_Solicitud' => $solicitud->ID_Solicitud,
                'ID_Revisor' => (int) Auth::id(),
                'ID_Asignador' => (int) Auth::id(),
                'Fecha_Asignacion' => now(),
            ]);

            Revision::create([
                'ID_Asignacion' => $asignacionActiva->ID_Asignacion,
                'Resultado' => 'En Revisión',
                'Fecha_Revision' => now(),
            ]);

            $solicitud->load('asignaciones.revisor', 'asignaciones.revisiones');
        }

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

