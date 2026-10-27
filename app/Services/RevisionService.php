<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AsignacionRevisor;
use App\Models\Bitacora;
use App\Models\Observacion;
use App\Models\Respuesta;
use App\Models\Revision;
use App\Models\Usuario;
use App\Models\VersionDoc;
use DomainException;
use Illuminate\Support\Facades\DB;

class RevisionService
{
    /**
     * Inicia una nueva revisión técnica formal vinculada a la asignación de revisor.
     */
    public function iniciarRevision(AsignacionRevisor $asignacion, Usuario $revisor): Revision
    {
        return DB::transaction(function () use ($asignacion, $revisor): Revision {
            $revision = Revision::create([
                'ID_Asignacion' => $asignacion->ID_Asignacion,
                'Resultado' => 'En Revisión',
                'Fecha_Revision' => now(),
            ]);

            // Regla: Auditoría y Bitácora obligatoria
            Bitacora::create([
                'ID_Usuarios' => $revisor->ID_Usuarios,
                'Accion' => 'Inicio de Revisión Técnica',
                'Descripcion' => sprintf(
                    'Se inició la revisión #%d para la asignación #%d (Solicitud #%d).',
                    $revision->ID_Revision,
                    $asignacion->ID_Asignacion,
                    $asignacion->ID_Solicitud
                ),
                'Fecha_Creacion' => now(),
            ]);

            return $revision;
        });
    }

    /**
     * Registra una observación técnica con estado 'Abierta'.
     */
    public function registrarObservacion(Revision $revision, string $detalles, Usuario $revisor): Observacion
    {
        return DB::transaction(function () use ($revision, $detalles, $revisor): Observacion {
            $observacion = Observacion::create([
                'ID_Revision' => $revision->ID_Revision,
                'Detalles' => $detalles,
                'Estado_Obs' => 'Abierta',
            ]);

            // Regla: Auditoría y Bitácora obligatoria
            Bitacora::create([
                'ID_Usuarios' => $revisor->ID_Usuarios,
                'Accion' => 'Apertura de Observación',
                'Descripcion' => sprintf(
                    'Observación #%d emitida en la revisión #%d. Detalles: %s',
                    $observacion->ID_Observacion,
                    $revision->ID_Revision,
                    substr($detalles, 0, 100)
                ),
                'Fecha_Creacion' => now(),
            ]);

            return $observacion;
        });
    }

    /**
     * Registra la respuesta o subsanación del ciudadano hacia una observación abierta.
     */
    public function subsanarObservacion(
        Observacion $observacion,
        string $mensaje,
        ?VersionDoc $nuevaVersionDoc,
        Usuario $ciudadano
    ): Respuesta {
        return DB::transaction(function () use ($observacion, $mensaje, $nuevaVersionDoc, $ciudadano): Respuesta {
            $respuesta = Respuesta::create([
                'ID_Observacion' => $observacion->ID_Observacion,
                'ID_Version_Doc' => $nuevaVersionDoc?->ID_Version_Doc,
                'Mensaje_Respuesta' => $mensaje,
                'Fecha_Respuesta' => now(),
            ]);

            $observacion->update([
                'Estado_Obs' => 'Subsanada',
            ]);

            // Regla: Auditoría y Bitácora obligatoria
            Bitacora::create([
                'ID_Usuarios' => $ciudadano->ID_Usuarios,
                'Accion' => 'Subsanación de Observación',
                'Descripcion' => sprintf(
                    'Observación #%d subsanada con respuesta #%d. Versión de documento adjunto: %s',
                    $observacion->ID_Observacion,
                    $respuesta->ID_Respuesta,
                    $nuevaVersionDoc?->ID_Version_Doc ?? 'Ninguna'
                ),
                'Fecha_Creacion' => now(),
            ]);

            return $respuesta;
        });
    }

    /**
     * Dictamina la revisión emitiendo un resultado final (Favorable, Observado, Desfavorable).
     *
     * @throws DomainException
     */
    public function dictaminarRevision(Revision $revision, string $resultado, Usuario $revisor): Revision
    {
        $resultadosValidos = ['Favorable', 'Observado', 'Desfavorable', 'En Revisión'];
        if (! in_array($resultado, $resultadosValidos, true)) {
            throw new DomainException(sprintf('Resultado "%s" no es válido. Debe ser uno de: %s', $resultado, implode(', ', $resultadosValidos)));
        }

        return DB::transaction(function () use ($revision, $resultado, $revisor): Revision {
            $revision->update([
                'Resultado' => $resultado,
                'Fecha_Revision' => now(),
            ]);

            // Regla: Auditoría y Bitácora obligatoria (Todo cambio de estado en una revisión)
            Bitacora::create([
                'ID_Usuarios' => $revisor->ID_Usuarios,
                'Accion' => 'Cambio de Estado en Revisión',
                'Descripcion' => sprintf(
                    'La revisión #%d fue dictaminada con resultado: "%s".',
                    $revision->ID_Revision,
                    $resultado
                ),
                'Fecha_Creacion' => now(),
            ]);

            return $revision;
        });
    }
}
