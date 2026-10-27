<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\Resolucion;
use App\Models\Solicitud;
use App\Models\Usuario;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class ResolucionService
{
    /**
     * Emite la resolución institucional final para una solicitud y la registra en bitácora.
     *
     * @throws AuthorizationException|DomainException|\Throwable
     */
    public function emitirResolucion(
        Solicitud $solicitud,
        Usuario $supervisor,
        string $tipoResolucion
    ): Resolucion {
        // Validar que el usuario tenga rol de Supervisor o Administrador
        if (! $supervisor->relationLoaded('roles')) {
            $supervisor->load('roles');
        }

        $roles = $supervisor->roles->pluck('Nombre')->map(fn ($r) => strtolower((string) $r))->all();
        if (! in_array('supervisor', $roles, true) && ! in_array('administrador', $roles, true)) {
            throw new AuthorizationException('Solo un usuario con rol de Supervisor o Administrador puede emitir resoluciones finales.');
        }

        $tiposValidos = ['Aprobada', 'Rechazada', 'Improcedente', 'Concluida'];
        if (! in_array($tipoResolucion, $tiposValidos, true)) {
            throw new DomainException(sprintf(
                'Tipo de resolución "%s" inválido. Tipos permitidos: %s',
                $tipoResolucion,
                implode(', ', $tiposValidos)
            ));
        }

        return DB::transaction(function () use ($solicitud, $supervisor, $tipoResolucion): Resolucion {
            $resolucion = Resolucion::create([
                'ID_Solicitud' => $solicitud->ID_Solicitud,
                'ID_Supervisor' => $supervisor->ID_Usuarios,
                'Fecha_Emision_Resolucion' => now(),
                'Tipo_Resolucion' => $tipoResolucion,
            ]);

            // Regla: Auditoría y Bitácora obligatoria (Emisión de resolución)
            Bitacora::create([
                'ID_Usuarios' => $supervisor->ID_Usuarios,
                'Accion' => 'Emisión de Resolución',
                'Descripcion' => sprintf(
                    'Resolución final #%d emitida con dictamen "%s" para la solicitud #%d por el supervisor #%d (%s).',
                    $resolucion->ID_Resolucion,
                    $tipoResolucion,
                    $solicitud->ID_Solicitud,
                    $supervisor->ID_Usuarios,
                    $supervisor->Nombre
                ),
                'Fecha_Creacion' => now(),
            ]);

            return $resolucion;
        });
    }
}
