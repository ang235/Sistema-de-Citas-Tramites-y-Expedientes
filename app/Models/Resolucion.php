<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $ID_Resolucion
 * @property int $ID_Solicitud
 * @property int $ID_Supervisor
 * @property Carbon|null $Fecha_Emision_Resolucion
 * @property string $Tipo_Resolucion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Solicitud $solicitud
 * @property-read Usuario $supervisor
 */
#[Fillable(['ID_Solicitud', 'ID_Supervisor', 'Fecha_Emision_Resolucion', 'Tipo_Resolucion'])]
class Resolucion extends Model
{
    use HasFactory;

    protected $table = 'resoluciones';

    protected $primaryKey = 'ID_Resolucion';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Fecha_Emision_Resolucion' => 'datetime',
        ];
    }

    /**
     * Solicitud o expediente sobre el cual recae la resolución final.
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'ID_Solicitud', 'ID_Solicitud');
    }

    /**
     * Usuario supervisor o autoridad que emite y firma la resolución.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_Supervisor', 'ID_Usuarios');
    }
}
