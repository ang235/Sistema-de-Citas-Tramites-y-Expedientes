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
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

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
                $q->where('ID_Revisor', $idUsuario)
                    ->where(fn ($sub) => $sub->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion'));
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
                'documentos.versionesDoc',
                'asignaciones.revisor',
                'asignaciones.revisiones.observaciones',
                'resoluciones',
            ]);

            if ($esRevisor && ! $esSupervisor && ! $esAdmin) {
                $querySelected->whereHas('asignaciones', function ($q) use ($idUsuario): void {
                    $q->where('ID_Revisor', $idUsuario)
                        ->where(fn ($sub) => $sub->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion'));
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
                'documentos.versionesDoc',
                'asignaciones.revisor',
                'asignaciones.revisiones.observaciones',
                'resoluciones',
            ])->find($solicitudes->first()->ID_Solicitud);
        }

        $asignacionActiva = null;
        if ($solicitudSeleccionada) {
            $asignacionActiva = $solicitudSeleccionada->asignaciones
                ->filter(fn ($a) => (int) $a->ID_Revisor === $idUsuario && in_array($a->Estado_Asignacion, ['Activo', null]))
                ->last()
                ?? (($esAdmin || $esSupervisor) ? ($solicitudSeleccionada->asignaciones->filter(fn ($a) => in_array($a->Estado_Asignacion, ['Activo', null]))->last() ?? $solicitudSeleccionada->asignaciones->last()) : null);

            // Si es Admin o Supervisor y el expediente aún no tiene asignación, asegurar una asignación operativa
            if (! $asignacionActiva && ($esAdmin || $esSupervisor)) {
                $asignacionActiva = AsignacionRevisor::firstOrCreate(
                    [
                        'ID_Solicitud' => $solicitudSeleccionada->ID_Solicitud,
                        'ID_Revisor' => $idUsuario,
                    ],
                    [
                        'Fecha_Asignacion' => now(),
                        'Estado_Asignacion' => 'Activo',
                    ]
                );
            }
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

    public function show(Request $request, int $idSolicitud): View|RedirectResponse
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
            'documentos.versionesDoc',
            'asignaciones.revisor',
            'asignaciones.revisiones.observaciones.respuestas.versionDoc',
            'resoluciones.supervisor',
        ])->findOrFail($idSolicitud);

        $asignacionPropia = $solicitud->asignaciones
            ->filter(fn ($a) => (int) $a->ID_Revisor === $idUsuario && in_array($a->Estado_Asignacion, ['Activo', null]))
            ->last();

        $tieneResolucion = $solicitud->resoluciones->isNotEmpty();
        $esConcluido = $tieneResolucion;
        $esRevisorPuro = $usuario->esRevisor() && ! $usuario->esSupervisor() && ! $usuario->esAdministrador();
        $esSoloLectura = $request->boolean('readonly') || $tieneResolucion || ($esRevisorPuro && ! $asignacionPropia);

        // Si es revisor y no es supervisor ni admin, verificar asignación obligatoria previa por el supervisor
        // O si ya fue concluido / visto en modo auditoría o solo lectura
        if ($esRevisorPuro) {
            if (! $asignacionPropia && ! $tieneResolucion && ! $request->boolean('readonly')) {
                abort(403, 'Acceso no autorizado: Este expediente aún no ha sido asignado a su mesa por el supervisor en la Matriz de Cargas.');
            }

            $asignacionActiva = $asignacionPropia;
        } else {
            $asignacionActiva = $solicitud->asignaciones->filter(fn ($a) => in_array($a->Estado_Asignacion, ['Activo', null]))->last()
                ?? $solicitud->asignaciones->last();

            if (! $asignacionActiva) {
                $asignacionActiva = AsignacionRevisor::firstOrCreate(
                    [
                        'ID_Solicitud' => $solicitud->ID_Solicitud,
                        'ID_Revisor' => $idUsuario,
                    ],
                    [
                        'Fecha_Asignacion' => now(),
                        'Estado_Asignacion' => 'Activo',
                    ]
                );
            }
        }

        return view('portal.evaluacion', [
            'solicitud' => $solicitud,
            'asignacionActiva' => $asignacionActiva,
            'tieneResolucion' => $tieneResolucion,
            'esSoloLectura' => $esSoloLectura,
            'esConcluido' => $esConcluido,
        ]);
    }

    public function evaluarDocumento(Request $request, int $idDocumento): RedirectResponse
    {
        $validated = $request->validate([
            'estado_doc' => ['required', 'string', 'in:Aprobado,Observado,Rechazado,Pendiente'],
        ]);

        /** @var Documento $documento */
        $documento = Documento::with('solicitud.resoluciones')->findOrFail($idDocumento);
        $solicitud = $documento->solicitud;

        if ($solicitud && $solicitud->resoluciones->isNotEmpty()) {
            return back()->with('error', 'El expediente ya se encuentra formalmente concluido con resolución emitida. No se permiten modificaciones.');
        }

        /** @var Usuario $usuario */
        $usuario = Auth::user();
        if ($usuario->esRevisor() && ! $usuario->esSupervisor() && ! $usuario->esAdministrador()) {
            $asignado = AsignacionRevisor::where('ID_Solicitud', $documento->ID_Solicitud)
                ->where('ID_Revisor', $usuario->ID_Usuarios)
                ->where(fn ($q) => $q->where('Estado_Asignacion', 'Activo')->orWhereNull('Estado_Asignacion'))
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
        $asignacion = AsignacionRevisor::with('solicitud.resoluciones')->findOrFail($idAsignacion);
        $solicitud = $asignacion->solicitud;

        if ($solicitud && $solicitud->resoluciones->isNotEmpty()) {
            return back()->with('error', 'El expediente ya se encuentra concluido y cuenta con resolución formal emitida. No se pueden registrar más dictámenes.');
        }

        $estadoSol = $solicitud->Estado_Solicitud;
        if (! in_array($estadoSol, ['Pendiente', 'En_Revision'], true)) {
            if ($estadoSol === 'Observado') {
                return back()->with('error', 'El expediente tiene observaciones técnicas pendientes de subsanación por el ciudadano. No es posible emitir un nuevo dictamen hasta recibir su respuesta.');
            }
            if ($estadoSol === 'Aprobado') {
                return back()->with('error', 'El expediente ya cuenta con dictamen aprobatorio emitido. Se encuentra en etapa de supervisión para su resolución final.');
            }

            return back()->with('error', "El expediente se encuentra en estado '{$estadoSol}' y no admite nuevos dictámenes sin una subsanación previa.");
        }

        /** @var Usuario $usuario */
        $usuario = Auth::user();
        if ($usuario->esRevisor() && ! $usuario->esSupervisor() && ! $usuario->esAdministrador()) {
            if ($asignacion->ID_Revisor !== $usuario->ID_Usuarios || ! in_array($asignacion->Estado_Asignacion, ['Activo', null])) {
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
                    // Si se aprueba, cerrar cualquier observación previa
                    Observacion::whereHas('revision.asignacion', fn ($q) => $q->where('ID_Solicitud', $solicitud->ID_Solicitud))
                        ->where('Estado_Obs', 'Abierta')
                        ->update(['Estado_Obs' => 'Subsanada']);
                } elseif ($resultado === 'Rechazado') {
                    $solicitud->update(['Estado_Solicitud' => 'Rechazado']);
                    // Si se rechaza, cerrar observaciones previas para no dejar subsanaciones huérfanas
                    Observacion::whereHas('revision.asignacion', fn ($q) => $q->where('ID_Solicitud', $solicitud->ID_Solicitud))
                        ->where('Estado_Obs', 'Abierta')
                        ->update(['Estado_Obs' => 'Subsanada']);
                }
            }

            $folio = $solicitud?->folio?->Codigo_Folio ?? "EXP-{$asignacion->ID_Solicitud}";
            Bitacora::create([
                'ID_Usuarios' => (int) Auth::id(),
                'Accion' => 'Expediente Calificado: Dictamen de revisión',
                'Descripcion' => "Expediente {$folio} (#{$asignacion->ID_Solicitud}) calificado con dictamen técnico: {$resultado}.",
                'Fecha_Creacion' => now(),
            ]);
        });

        return redirect()->route('portal.mesa-dictamen')
            ->with('success', 'Dictamen técnico registrado exitosamente.');
    }

    /**
     * Permite la visualización o descarga segura del archivo digital cargado por el ciudadano.
     */
    public function verArchivo(Request $request, int $idDocumento): Response|BinaryFileResponse
    {
        /** @var Usuario|null $usuario */
        $usuario = Auth::user();
        abort_if(! $usuario, 401);

        /** @var Documento $documento */
        $documento = Documento::with(['solicitud', 'versionesDoc', 'requisito'])->findOrFail($idDocumento);

        // Si es ciudadano, solo puede consultar documentos de sus propias solicitudes
        if ($usuario->esCiudadano() && (int) $documento->solicitud?->ID_Solicitante !== (int) $usuario->ID_Usuarios) {
            abort(403, 'No tiene autorización para visualizar este archivo.');
        }

        $versionId = $request->query('version_id');
        $version = $versionId
            ? $documento->versionesDoc->firstWhere('ID_Version_Doc', (int) $versionId)
            : $documento->versionesDoc->last();
        $ruta = $version?->Ruta_Archivo;

        // Búsqueda de respaldo por carpeta de expediente si no está la ruta exacta
        if (! $ruta || ! Storage::disk('local')->exists($ruta)) {
            $dir = "expedientes/{$documento->ID_Solicitud}";
            $archivos = Storage::disk('local')->files($dir);
            if (! empty($archivos)) {
                $docIds = $documento->solicitud->documentos()->orderBy('ID_Documento')->pluck('ID_Documento')->values();
                $pos = $docIds->search($idDocumento);
                $ruta = $archivos[$pos !== false ? $pos : 0] ?? $archivos[0];
            }
        }

        if (! $ruta || ! Storage::disk('local')->exists($ruta)) {
            abort(404, 'El archivo digital no fue encontrado en el almacenamiento institucional.');
        }

        $nombreDescarga = $version?->Nombre_Original ?? (($documento->requisito?->Nombre ?? 'documento').'.pdf');
        $nombreLimpio = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $nombreDescarga);

        return Storage::disk('local')->response(
            $ruta,
            $nombreLimpio,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => ($request->query('descargar') ? 'attachment' : 'inline').'; filename="'.$nombreLimpio.'"',
            ]
        );
    }
}
