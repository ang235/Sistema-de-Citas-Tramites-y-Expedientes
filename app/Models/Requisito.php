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
 * @property int $ID_Requisitos
 * @property int $ID_Version_Tramite
 * @property string $Nombre
 * @property string $Prioridad
 * @property string $Formato
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read VersionTramite $versionTramite
 * @property-read Collection<int, Documento> $documentos
 */
#[Fillable(['ID_Version_Tramite', 'Nombre', 'Prioridad', 'Formato'])]
class Requisito extends Model
{
    use HasFactory;

    protected $table = 'requisitos';

    protected $primaryKey = 'ID_Requisitos';

    public $incrementing = true;

    protected $keyType = 'int';

    /**
     * Versión de trámite a la que corresponde el requisito.
     */
    public function versionTramite(): BelongsTo
    {
        return $this->belongsTo(VersionTramite::class, 'ID_Version_Tramite', 'ID_Version_Tramite');
    }

    /**
     * Documentos adjuntados en distintas solicitudes que corresponden a este requisito.
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'ID_Requisitos', 'ID_Requisitos');
    }

    /**
     * Determina si el requisito es obligatorio.
     */
    public function esObligatorio(): bool
    {
        return strcasecmp((string) $this->Prioridad, 'Obligatorio') === 0;
    }
}
