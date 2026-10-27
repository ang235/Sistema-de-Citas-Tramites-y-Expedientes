<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\DatoSolicitud;
use App\Models\Documento;
use App\Models\Folio;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Models\VersionTramite;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class SolicitudService
{
    /**
     * Crea un nuevo expediente/solicitud ligándolo permanentemente a la versión
     * normativa activa, generando un folio único e inicializando sus requisitos.
     *
     * @throws \Throwable
     */
    public function crearSolicitud(
        Usuario $solicitante,
        int $idVersionTramite,
        ?string $descripcion = null
    ): Solicitud {
        return DB::transaction(function () use ($solicitante, $idVersionTramite, $descripcion): Solicitud {
            /** @var VersionTramite $versionTramite */
            $versionTramite = VersionTramite::with('requisitos')->findOrFail($idVersionTramite);

            // 1. Regla: Inmutabilidad y Versionado - se congela la versión actual
            $solicitud = Solicitud::create([
                'ID_Solicitante' => $solicitante->ID_Usuarios,
                'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
                'Fecha_Creacion_Solicitud' => now(),
            ]);

            // 2. Regla: Generación de Folios Únicos, Consecutivos e Irrepetibles
            $year = date('Y');
            $ultimoFolio = Folio::query()
                ->where('Codigo_Folio', 'LIKE', "SETC-{$year}-%")
                ->lockForUpdate()
                ->latest('ID_Folio')
                ->first();

            $consecutivo = 1;
            if ($ultimoFolio !== null && preg_match('/SETC-\d{4}-(\d+)/', (string) $ultimoFolio->Codigo_Folio, $matches)) {
                $consecutivo = ((int) $matches[1]) + 1;
            }

            $codigoFolio = sprintf('SETC-%s-%05d', $year, $consecutivo);

            Folio::create([
                'ID_Solicitud' => $solicitud->ID_Solicitud,
                'Codigo_Folio' => $codigoFolio,
                'Fecha_Emision' => now(),
            ]);

            // 3. Datos complementarios de la solicitud si existen
            if ($descripcion !== null && trim($descripcion) !== '') {
                DatoSolicitud::create([
                    'ID_Solicitud' => $solicitud->ID_Solicitud,
                    'Descripcion_Sol' => trim($descripcion),
                ]);
            }

            // 4. Inicialización de documentos requeridos según la versión normativa
            foreach ($versionTramite->requisitos as $requisito) {
                Documento::create([
                    'ID_Solicitud' => $solicitud->ID_Solicitud,
                    'ID_Requisitos' => $requisito->ID_Requisitos,
                    'Estado_Doc' => 'Pendiente',
                ]);
            }

            // 5. Regla: Auditoría y Bitácora obligatoria
            Bitacora::create([
                'ID_Usuarios' => $solicitante->ID_Usuarios,
                'Accion' => 'Creación de Expediente',
                'Descripcion' => sprintf(
                    'Se generó el expediente #%d con Folio %s para la versión de trámite #%s.',
                    $solicitud->ID_Solicitud,
                    $codigoFolio,
                    $versionTramite->No_Version
                ),
                'Fecha_Creacion' => now(),
            ]);

            return $solicitud->load(['folio', 'versionTramite', 'documentos.requisito']);
        });
    }

    /**
     * Consulta el expediente aplicando la regla de Privacidad y Aislamiento del Solicitante.
     *
     * @throws AuthorizationException
     */
    public function consultarExpediente(Solicitud $solicitud, Usuario $usuario): array
    {
        // Cargar roles del usuario si no están en memoria
        if (! $usuario->relationLoaded('roles')) {
            $usuario->load('roles');
        }

        $roles = $usuario->roles->pluck('Nombre')->map(fn ($r) => strtolower((string) $r))->all();
        $esPersonal = in_array('administrador', $roles, true)
            || in_array('supervisor', $roles, true)
            || in_array('revisor', $roles, true);

        // Regla: Aislamiento del solicitante
        if (! $esPersonal && (int) $solicitud->ID_Solicitante !== (int) $usuario->ID_Usuarios) {
            throw new AuthorizationException('Acceso denegado: solo el solicitante titular o personal autorizado puede consultar este expediente.');
        }

        $solicitud->load([
            'solicitante',
            'versionTramite.tipoTramite',
            'folio',
            'datosSolicitud',
            'documentos.requisito',
            'documentos.versionesDoc',
            'asignaciones.revisor',
            'asignaciones.revisiones.observaciones.respuestas',
            'citas',
            'resoluciones.supervisor',
        ]);

        return [
            'solicitud' => $solicitud,
            'folio' => $solicitud->folio?->Codigo_Folio,
            'fecha_creacion' => $solicitud->Fecha_Creacion_Solicitud,
            'tramite' => $solicitud->versionTramite?->tipoTramite?->Nombre,
            'version_tramite' => $solicitud->versionTramite?->No_Version,
            'documentos_completos' => (new DocumentoService)->validarRequisitosCompletos($solicitud),
        ];
    }
}
