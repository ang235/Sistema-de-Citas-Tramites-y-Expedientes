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
 * @property int $ID_Version_Doc
 * @property int $ID_Documento
 * @property Carbon|null $Fecha_Carga
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Documento $documento
 * @property-read Collection<int, Respuesta> $respuestas
 */
#[Fillable(['ID_Documento', 'Fecha_Carga'])]
class VersionDoc extends Model
{
    use HasFactory;

    protected $table = 'versiones_doc';

    protected $primaryKey = 'ID_Version_Doc';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Fecha_Carga' => 'datetime',
        ];
    }

    /**
     * Documento principal al que pertenece esta versión.
     */
    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'ID_Documento', 'ID_Documento');
    }

    /**
     * Respuestas o subsanaciones de observaciones en las que se adjuntó esta versión.
     */
    public function respuestas(): HasMany
    {
        return $this->hasMany(Respuesta::class, 'ID_Version_Doc', 'ID_Version_Doc');
    }
}
