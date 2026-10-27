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
 * @property int $ID_Revision
 * @property int $ID_Asignacion
 * @property string $Resultado
 * @property Carbon|null $Fecha_Revision
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AsignacionRevisor $asignacionRevisor
 * @property-read Collection<int, Observacion> $observaciones
 */
#[Fillable(['ID_Asignacion', 'Resultado', 'Fecha_Revision'])]
class Revision extends Model
{
    use HasFactory;

    protected $table = 'revisiones';

    protected $primaryKey = 'ID_Revision';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Fecha_Revision' => 'datetime',
        ];
    }

    /**
     * Asignación de revisor a la que corresponde este proceso de revisión.
     */
    public function asignacionRevisor(): BelongsTo
    {
        return $this->belongsTo(AsignacionRevisor::class, 'ID_Asignacion', 'ID_Asignacion');
    }

    /**
     * Observaciones técnicas emitidas durante la revisión.
     */
    public function observaciones(): HasMany
    {
        return $this->hasMany(Observacion::class, 'ID_Revision', 'ID_Revision');
    }
}
