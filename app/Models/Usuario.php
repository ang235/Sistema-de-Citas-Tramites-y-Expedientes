<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $ID_Usuarios
 * @property string $Nombre
 * @property string $Email
 * @property string $Contrasena
 * @property Carbon|null $Fecha_Creacion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Rol> $roles
 * @property-read Collection<int, Solicitud> $solicitudes
 * @property-read Collection<int, Cita> $citas
 * @property-read Collection<int, Bitacora> $bitacoras
 * @property-read Collection<int, AsignacionRevisor> $asignacionesComoRevisor
 * @property-read Collection<int, AsignacionRevisor> $asignacionesComoAsignador
 * @property-read Collection<int, Resolucion> $resolucionesSupervisadas
 */
#[Fillable(['Nombre', 'Email', 'Contrasena', 'Fecha_Creacion'])]
#[Hidden(['Contrasena', 'remember_token'])]
class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'ID_Usuarios';

    public $incrementing = true;

    protected $keyType = 'int';

    public function getAuthPasswordName(): string
    {
        return 'Contrasena';
    }

    protected function casts(): array
    {
        return [
            'Fecha_Creacion' => 'datetime',
            'Contrasena' => 'hashed',
        ];
    }

    /**
     * Roles asignados al usuario.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'usuarios_roles',
            'ID_Usuarios',
            'ID_Rol'
        )->withTimestamps();
    }

    /**
     * Solicitudes iniciadas por el usuario como solicitante.
     */
    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'ID_Solicitante', 'ID_Usuarios');
    }

    /**
     * Citas programadas por o para el usuario.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'ID_Usuarios', 'ID_Usuarios');
    }

    /**
     * Registros de bitácora generados por las acciones del usuario.
     */
    public function bitacoras(): HasMany
    {
        return $this->hasMany(Bitacora::class, 'ID_Usuarios', 'ID_Usuarios');
    }

    /**
     * Expedientes/solicitudes asignadas a este usuario para su revisión técnica.
     */
    public function asignacionesComoRevisor(): HasMany
    {
        return $this->hasMany(AsignacionRevisor::class, 'ID_Revisor', 'ID_Usuarios');
    }

    /**
     * Asignaciones emitidas por este usuario (supervisor/asignador).
     */
    public function asignacionesComoAsignador(): HasMany
    {
        return $this->hasMany(AsignacionRevisor::class, 'ID_Asignador', 'ID_Usuarios');
    }

    /**
     * Resoluciones emitidas por este usuario como supervisor.
     */
    public function resolucionesSupervisadas(): HasMany
    {
        return $this->hasMany(Resolucion::class, 'ID_Supervisor', 'ID_Usuarios');
    }

    /**
     * Solicitudes de recuperación de contraseña del usuario.
     */
    public function recuperacionesContrasena(): HasMany
    {
        return $this->hasMany(RecuperacionContrasena::class, 'ID_Usuarios', 'ID_Usuarios');
    }

    /**
     * Comprueba si el usuario tiene un rol determinado.
     */
    public function tieneRol(string $nombreRol): bool
    {
        return $this->roles->contains('Nombre', $nombreRol);
    }
}
