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
 * @property int $ID_Documento
 * @property int $ID_Solicitud
 * @property int $ID_Requisitos
 * @property string $Estado_Doc
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Solicitud $solicitud
 * @property-read Requisito $requisito
 * @property-read Collection<int, VersionDoc> $versionesDoc
 */
#[Fillable(['ID_Solicitud', 'ID_Requisitos', 'Estado_Doc'])]
class Documento extends Model
{
    use HasFactory;

    protected $table = 'documento';

    protected $primaryKey = 'ID_Documento';

    public $incrementing = true;

    protected $keyType = 'int';

    /**
     * Solicitud a la que pertenece este expediente o documento.
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'ID_Solicitud', 'ID_Solicitud');
    }

    /**
     * Requisito normativo que satisface este documento.
     */
    public function requisito(): BelongsTo
    {
        return $this->belongsTo(Requisito::class, 'ID_Requisitos', 'ID_Requisitos');
    }

    /**
     * Versiones o iteraciones de carga del archivo.
     */
    public function versionesDoc(): HasMany
    {
        return $this->hasMany(VersionDoc::class, 'ID_Documento', 'ID_Documento');
    }

    /**
     * Última versión cargada del documento.
     */
    public function ultimaVersion(): ?VersionDoc
    {
        return $this->versionesDoc()->latest('ID_Version_Doc')->first();
    }
}
