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
 * @property int $ID_Asignacion
 * @property int $ID_Solicitud
 * @property int $ID_Revisor
 * @property int $ID_Asignador
 * @property Carbon|null $Fecha_Asignacion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Solicitud $solicitud
 * @property-read Usuario $revisor
 * @property-read Usuario $asignador
 * @property-read Collection<int, Revision> $revisiones
 */
#[Fillable(['ID_Solicitud', 'ID_Revisor', 'ID_Asignador', 'Fecha_Asignacion'])]
class AsignacionRevisor extends Model
{
    use HasFactory;

    protected $table = 'asignacion_revisor';

    protected $primaryKey = 'ID_Asignacion';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Fecha_Asignacion' => 'datetime',
        ];
    }

    /**
     * Solicitud o expediente asignado.
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'ID_Solicitud', 'ID_Solicitud');
    }

    /**
     * Usuario técnico o revisor asignado.
     */
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_Revisor', 'ID_Usuarios');
    }

    /**
     * Usuario supervisor que realizó la asignación.
     */
    public function asignador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_Asignador', 'ID_Usuarios');
    }

    /**
     * Evaluaciones y revisiones técnicas ejecutadas bajo esta asignación.
     */
    public function revisiones(): HasMany
    {
        return $this->hasMany(Revision::class, 'ID_Asignacion', 'ID_Asignacion');
    }
}
