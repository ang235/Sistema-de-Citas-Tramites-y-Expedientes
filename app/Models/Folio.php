<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $ID_Folio
 * @property int $ID_Solicitud
 * @property string $Codigo_Folio
 * @property Carbon|null $Fecha_Emision
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Solicitud $solicitud
 */
#[Fillable(['ID_Solicitud', 'Codigo_Folio', 'Fecha_Emision'])]
class Folio extends Model
{
    use HasFactory;

    protected $table = 'folio';

    protected $primaryKey = 'ID_Folio';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Fecha_Emision' => 'datetime',
        ];
    }

    /**
     * Solicitud / expediente al que pertenece el folio.
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'ID_Solicitud', 'ID_Solicitud');
    }
}
