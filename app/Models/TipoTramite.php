<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $ID_Tipo_Tramite
 * @property string $Nombre
 * @property string|null $Descripcion
 * @property string $Estado_Tipo_Tr
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, VersionTramite> $versiones
 */
#[Fillable(['Nombre', 'Descripcion', 'Estado_Tipo_Tr'])]
class TipoTramite extends Model
{
    use HasFactory;

    protected $table = 'tipo_tramite';

    protected $primaryKey = 'ID_Tipo_Tramite';

    public $incrementing = true;

    protected $keyType = 'int';

    /**
     * Versiones históricas o normativas asociadas al trámite.
     */
    public function versiones(): HasMany
    {
        return $this->hasMany(VersionTramite::class, 'ID_Tipo_Tramite', 'ID_Tipo_Tramite');
    }

    /**
     * Obtiene la versión actualmente vigente del trámite.
     */
    public function versionVigente(): ?VersionTramite
    {
        return $this->versiones()->where('Estado_Ver_Tr', 'Vigente')->latest('ID_Version_Tramite')->first();
    }
}
