<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $ID_Usuario_Roles
 * @property int $ID_Usuarios
 * @property int $ID_Rol
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Usuario $usuario
 * @property-read Rol $rol
 */
#[Fillable(['ID_Usuarios', 'ID_Rol'])]
class UsuarioRol extends Pivot
{
    use HasFactory;

    protected $table = 'usuarios_roles';

    protected $primaryKey = 'ID_Usuario_Roles';

    public $incrementing = true;

    protected $keyType = 'int';

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_Usuarios', 'ID_Usuarios');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'ID_Rol', 'ID_Rol');
    }
}
