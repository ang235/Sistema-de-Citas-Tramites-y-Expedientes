<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $ID_Observacion
 * @property int $ID_Revision
 * @property string $Detalles
 * @property string $Estado_Obs
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Revision $revision
 * @property-read Collection<int, Respuesta> $respuestas
 */
#[Fillable(['ID_Revision', 'Detalles', 'Estado_Obs'])]
class Observacion extends Model
{
    use HasFactory;

    protected $table = 'observaciones';

    protected $primaryKey = 'ID_Observacion';

    public $incrementing = true;

    protected $keyType = 'int';

    /**
     * Revisión en la cual fue levantada esta observación.
     */
    public function revision(): BelongsTo
    {
        return $this->belongsTo(Revision::class, 'ID_Revision', 'ID_Revision');
    }

    /**
     * Respuestas o subsanaciones enviadas por el ciudadano.
     */
    public function respuestas(): HasMany
    {
        return $this->hasMany(Respuesta::class, 'ID_Observacion', 'ID_Observacion');
    }

    /**
     * Verifica si la observación sigue abierta.
     */
    public function estaAbierta(): bool
    {
        return strcasecmp((string) $this->Estado_Obs, 'Abierta') === 0;
    }
}
