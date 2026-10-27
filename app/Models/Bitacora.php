<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $ID_Bitacora
 * @property int $ID_Usuarios
 * @property string $Accion
 * @property string|null $Descripcion
 * @property Carbon|null $Fecha_Creacion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Usuario $usuario
 */
#[Fillable(['ID_Usuarios', 'Accion', 'Descripcion', 'Fecha_Creacion'])]
class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'bitacora';

    protected $primaryKey = 'ID_Bitacora';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Fecha_Creacion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_Usuarios', 'ID_Usuarios');
    }
}
