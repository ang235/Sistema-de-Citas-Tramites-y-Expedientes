<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $ID_Contrasena
 * @property int $ID_Usuarios
 * @property string $Token
 * @property Carbon $Expiracion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Usuario $usuario
 */
#[Fillable(['ID_Usuarios', 'Token', 'Expiracion'])]
class RecuperacionContrasena extends Model
{
    use HasFactory;

    protected $table = 'recuperacion_contrasena';

    protected $primaryKey = 'ID_Contrasena';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Expiracion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_Usuarios', 'ID_Usuarios');
    }

    public function haExpirado(): bool
    {
        return $this->Expiracion->isPast();
    }
}
