<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $ID_Dts_Sol
 * @property int $ID_Solicitud
 * @property string|null $Descripcion_Sol
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Solicitud $solicitud
 */
#[Fillable(['ID_Solicitud', 'Descripcion_Sol'])]
class DatoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'datos_solicitud';

    protected $primaryKey = 'ID_Dts_Sol';

    public $incrementing = true;

    protected $keyType = 'int';

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'ID_Solicitud', 'ID_Solicitud');
    }
}
