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
 * @property int $ID_Version_Tramite
 * @property int $ID_Tipo_Tramite
 * @property string $No_Version
 * @property string $Estado_Ver_Tr
 * @property Carbon|null $Fecha_Inicio_Vig
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TipoTramite $tipoTramite
 * @property-read Collection<int, Requisito> $requisitos
 * @property-read Collection<int, Solicitud> $solicitudes
 */
#[Fillable(['ID_Tipo_Tramite', 'No_Version', 'Estado_Ver_Tr', 'Fecha_Inicio_Vig'])]
class VersionTramite extends Model
{
    use HasFactory;

    protected $table = 'version_tramite';

    protected $primaryKey = 'ID_Version_Tramite';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Fecha_Inicio_Vig' => 'date',
        ];
    }

    /**
     * Tipo de trámite al que pertenece esta versión.
     */
    public function tipoTramite(): BelongsTo
    {
        return $this->belongsTo(TipoTramite::class, 'ID_Tipo_Tramite', 'ID_Tipo_Tramite');
    }

    /**
     * Requisitos configurados para esta versión del trámite.
     */
    public function requisitos(): HasMany
    {
        return $this->hasMany(Requisito::class, 'ID_Version_Tramite', 'ID_Version_Tramite');
    }

    /**
     * Solicitudes ciudadanas generadas bajo esta versión del trámite.
     */
    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'ID_Version_Tramite', 'ID_Version_Tramite');
    }
}
