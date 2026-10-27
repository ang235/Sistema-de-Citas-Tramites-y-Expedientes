<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $ID_Rol
 * @property string $Nombre
 * @property string|null $Descripcion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Usuario> $usuarios
 */
#[Fillable(['Nombre', 'Descripcion'])]
class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $primaryKey = 'ID_Rol';

    public $incrementing = true;

    protected $keyType = 'int';

    /**
     * Usuarios asignados a este rol.
     */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            Usuario::class,
            'usuarios_roles',
            'ID_Rol',
            'ID_Usuarios'
        )->withTimestamps();
    }
}
