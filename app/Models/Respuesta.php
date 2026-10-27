<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $ID_Respuesta
 * @property int $ID_Observacion
 * @property int|null $ID_Version_Doc
 * @property string $Mensaje_Respuesta
 * @property Carbon|null $Fecha_Respuesta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Observacion $observacion
 * @property-read VersionDoc|null $versionDoc
 */
#[Fillable(['ID_Observacion', 'ID_Version_Doc', 'Mensaje_Respuesta', 'Fecha_Respuesta'])]
class Respuesta extends Model
{
    use HasFactory;

    protected $table = 'respuesta';

    protected $primaryKey = 'ID_Respuesta';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Fecha_Respuesta' => 'datetime',
        ];
    }

    /**
     * Observación a la que da respuesta.
     */
    public function observacion(): BelongsTo
    {
        return $this->belongsTo(Observacion::class, 'ID_Observacion', 'ID_Observacion');
    }

    /**
     * Versión del documento corregido o subsanado adjunto a la respuesta.
     */
    public function versionDoc(): BelongsTo
    {
        return $this->belongsTo(VersionDoc::class, 'ID_Version_Doc', 'ID_Version_Doc');
    }
}
