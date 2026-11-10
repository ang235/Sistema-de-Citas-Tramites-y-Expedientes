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
use App\Models\TipoTramite;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MesaDictamenController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();
        $idUsuario = (int) Auth::id();

        // Esta ventana es exclusiva del Revisor Técnico (y Administrador)
        if (! $usuario->esRevisor() && ! $usuario->esAdministrador()) {
            if ($usuario->esSupervisor()) {
                return redirect()->route('portal.supervisor-dashboard');
            }

            return redirect()->route('expedientes.index');
        }

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

        // Si es revisor y no es supervisor ni admin, filtrar estrictamente los expedientes que el supervisor le asignó
        if ($esRevisor && ! $esSupervisor && ! $esAdmin) {
            $query->whereHas('asignaciones', function ($q) use ($idUsuario): void {
                $q->where('ID_Revisor', $idUsuario)->where('Estado_Asignacion', 'Activo');
            });
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

        $solicitudes = $query->latest('ID_Solicitud')->paginate(15)->appends($request->query());

        // Seleccionar expediente activo para la consola de dictamen lateral
        $selectedId = $request->query('expediente');
        $solicitudSeleccionada = null;

        if ($selectedId) {
            $querySelected = Solicitud::with([
                'solicitante',
                'versionTramite.tipoTramite',
                'versionTramite.requisitos',
                'folio',
                'datosSolicitud',
                'documentos.requisito',
                'documentos.versiones',
                'asignaciones.revisor',
                'asignaciones.revisiones.observaciones',
                'resoluciones',
            ]);

            if ($esRevisor && ! $esSupervisor && ! $esAdmin) {
                $querySelected->whereHas('asignaciones', function ($q) use ($idUsuario): void {
                    $q->where('ID_Revisor', $idUsuario)->where('Estado_Asignacion', 'Activo');
                });
            }

            $solicitudSeleccionada = $querySelected->find($selectedId);
        }

        if (! $solicitudSeleccionada && $solicitudes->isNotEmpty()) {
            $solicitudSeleccionada = Solicitud::with([
                'solicitante',
                'versionTramite.tipoTramite',
                'versionTramite.requisitos',
                'folio',
                'datosSolicitud',
                'documentos.requisito',
                'documentos.versiones',
                'asignaciones.revisor',
                'asignaciones.revisiones.observaciones',
                'resoluciones',
            ])->find($solicitudes->first()->ID_Solicitud);
        }

        $asignacionActiva = null;
        if ($solicitudSeleccionada) {
            $asignacionActiva = $solicitudSeleccionada->asignaciones
                ->where('ID_Revisor', Auth::id())
                ->where('Estado_Asignacion', 'Activo')
                ->last()
                ?? ($esAdmin ? $solicitudSeleccionada->asignaciones->last() : null);
        }

        // Métricas de sala
        $totalAsignados = $esRevisor && ! $esSupervisor ? AsignacionRevisor::where('ID_Revisor', $idUsuario)->where('Estado_Asignacion', 'Activo')->count() : AsignacionRevisor::count();
        $revisadosHoy = Revision::whereDate('Fecha_Revision', today())->count();
        $enSubsanacion = Revision::where('Resultado', 'Observado')->count();

        $metricas = [
            'expedientes_asignados' => $totalAsignados,
            'revisados_hoy' => $revisadosHoy,
            'en_subsanacion' => $enSubsanacion,
            'prioritarios_sla' => Solicitud::doesntHave('asignaciones')->count(),
        ];

        $tiposTramite = TipoTramite::where('Estado_Tipo_Tr', 'Activo')->orderBy('Nombre')->get();

        return view('portal.mesa-dictamen', [
            'solicitudes' => $solicitudes,
            'solicitudSeleccionada' => $solicitudSeleccionada,
            'asignacionActiva' => $asignacionActiva,
            'metricas' => $metricas,
            'tiposTramite' => $tiposTramite,
            'buscar' => $search,
            'idTipoTramite' => $idTipoTramite,
        ]);
    }

    public function show(int $idSolicitud): View|RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();
        $idUsuario = (int) Auth::id();

        if (! $usuario || $usuario->esCiudadano()) {
            return redirect()->route('expedientes.index');
        }

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

        // Si es revisor y no es supervisor ni admin, verificar asignación obligatoria previa por el supervisor
        if ($usuario->esRevisor() && ! $usuario->esSupervisor() && ! $usuario->esAdministrador()) {
            $asignacionPropia = $solicitud->asignaciones
                ->where('ID_Revisor', $idUsuario)
                ->where('Estado_Asignacion', 'Activo')
                ->last();

            if (! $asignacionPropia) {
                abort(403, 'Acceso no autorizado: Este expediente aún no ha sido asignado a su mesa por el supervisor en la Matriz de Cargas.');
            }

            $asignacionActiva = $asignacionPropia;
        } else {
            $asignacionActiva = $solicitud->asignaciones->where('Estado_Asignacion', 'Activo')->last()
                ?? $solicitud->asignaciones->last();
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

        /** @var Usuario $usuario */
        $usuario = Auth::user();
        if ($usuario->esRevisor() && ! $usuario->esSupervisor() && ! $usuario->esAdministrador()) {
            $asignado = AsignacionRevisor::where('ID_Solicitud', $documento->ID_Solicitud)
                ->where('ID_Revisor', $usuario->ID_Usuarios)
                ->where('Estado_Asignacion', 'Activo')
                ->exists();

            if (! $asignado) {
                abort(403, 'No tiene autorización para calificar documentos de este expediente sin asignación previa del supervisor.');
            }
        }

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

        /** @var Usuario $usuario */
        $usuario = Auth::user();
        if ($usuario->esRevisor() && ! $usuario->esSupervisor() && ! $usuario->esAdministrador()) {
            if ($asignacion->ID_Revisor !== $usuario->ID_Usuarios || $asignacion->Estado_Asignacion !== 'Activo') {
                abort(403, 'No tiene autorización para dictaminar sobre esta asignación.');
            }
        }

        DB::transaction(function () use ($request, $asignacion): void {
            $resultado = $request->validated('resultado');

            $revision = Revision::create([
                'ID_Asignacion' => $asignacion->ID_Asignacion,
                'Resultado' => $resultado,
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

            // Sincronizar estado del expediente según el dictamen técnico
            $solicitud = $asignacion->solicitud;
            if ($solicitud) {
                if ($resultado === 'Observado') {
                    $solicitud->update(['Estado_Solicitud' => 'Observado']);
                } elseif ($resultado === 'Aprobado') {
                    $solicitud->update(['Estado_Solicitud' => 'Aprobado']);
                } elseif ($resultado === 'Rechazado') {
                    $solicitud->update(['Estado_Solicitud' => 'Rechazado']);
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
