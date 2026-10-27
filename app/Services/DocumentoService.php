<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\Documento;
use App\Models\Solicitud;
use App\Models\Usuario;
use App\Models\VersionDoc;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class DocumentoService
{
    /**
     * Sube una nueva versión del documento respetando la regla de inmutabilidad:
     * nunca sobreescribe el archivo anterior, sino que genera un nuevo VersionDoc.
     *
     * @throws AuthorizationException|\Throwable
     */
    public function subirNuevaVersion(
        Documento $documento,
        UploadedFile $archivo,
        Usuario $usuario
    ): VersionDoc {
        return DB::transaction(function () use ($documento, $archivo, $usuario): VersionDoc {
            $solicitud = $documento->solicitud;

            if ($solicitud === null) {
                $documento->load('solicitud');
                $solicitud = $documento->solicitud;
            }

            // Regla: Privacidad y Aislamiento del Solicitante
            if (! $usuario->relationLoaded('roles')) {
                $usuario->load('roles');
            }
            $roles = $usuario->roles->pluck('Nombre')->map(fn ($r) => strtolower((string) $r))->all();
            $esStaff = in_array('administrador', $roles, true) || in_array('revisor', $roles, true);

            if (! $esStaff && $solicitud !== null && (int) $solicitud->ID_Solicitante !== (int) $usuario->ID_Usuarios) {
                throw new AuthorizationException('No tiene autorización para cargar documentos en este expediente.');
            }

            // Regla: Control de versiones - Almacenamiento seguro e independiente (sin sobreescritura)
            $nombreArchivoUnico = sprintf(
                'doc_%d_v_%d_%s.%s',
                $documento->ID_Documento,
                now()->timestamp,
                bin2hex(random_bytes(4)),
                $archivo->getClientOriginalExtension()
            );

            $archivo->storeAs(
                sprintf('expedientes/%d', $documento->ID_Solicitud),
                $nombreArchivoUnico,
                'local'
            );

            // Crear registro en versiones_doc
            $versionDoc = VersionDoc::create([
                'ID_Documento' => $documento->ID_Documento,
                'Fecha_Carga' => now(),
            ]);

            // Actualizar estado del documento
            $documento->update([
                'Estado_Doc' => 'En Revisión',
            ]);

            // Regla: Auditoría y Bitácora obligatoria
            Bitacora::create([
                'ID_Usuarios' => $usuario->ID_Usuarios,
                'Accion' => 'Actualización de Documento',
                'Descripcion' => sprintf(
                    'Se cargó la versión #%d del documento #%d (Solicitud #%d). Archivo: %s',
                    $versionDoc->ID_Version_Doc,
                    $documento->ID_Documento,
                    $documento->ID_Solicitud,
                    $nombreArchivoUnico
                ),
                'Fecha_Creacion' => now(),
            ]);

            return $versionDoc;
        });
    }

    /**
     * Valida si todos los requisitos obligatorios de la solicitud cuentan con al menos una versión cargada.
     */
    public function validarRequisitosCompletos(Solicitud $solicitud): bool
    {
        $documentosObligatorios = $solicitud->documentos()
            ->whereHas('requisito', function ($query) {
                $query->whereRaw('LOWER(Prioridad) = ?', ['obligatorio']);
            })
            ->withCount('versionesDoc')
            ->get();

        if ($documentosObligatorios->isEmpty()) {
            return true;
        }

        // Cada documento obligatorio debe tener al menos una versión cargada
        return $documentosObligatorios->every(fn (Documento $doc) => $doc->versiones_doc_count > 0);
    }
}
