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
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $ID_Solicitud
 * @property int $ID_Solicitante
 * @property int $ID_Version_Tramite
 * @property Carbon|null $Fecha_Creacion_Solicitud
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Usuario $solicitante
 * @property-read VersionTramite $versionTramite
 * @property-read Folio|null $folio
 * @property-read DatoSolicitud|null $datosSolicitud
 * @property-read Collection<int, Documento> $documentos
 * @property-read Collection<int, AsignacionRevisor> $asignaciones
 * @property-read Collection<int, Cita> $citas
 * @property-read Collection<int, Resolucion> $resoluciones
 */
#[Fillable(['ID_Solicitante', 'ID_Version_Tramite', 'Fecha_Creacion_Solicitud'])]
class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $primaryKey = 'ID_Solicitud';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Fecha_Creacion_Solicitud' => 'datetime',
        ];
    }

    /**
     * Usuario ciudadano que inició el trámite.
     */
    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_Solicitante', 'ID_Usuarios');
    }

    /**
     * Versión normativa del trámite solicitado.
     */
    public function versionTramite(): BelongsTo
    {
        return $this->belongsTo(VersionTramite::class, 'ID_Version_Tramite', 'ID_Version_Tramite');
    }

    /**
     * Código de Folio único institucional emitido para el expediente.
     */
    public function folio(): HasOne
    {
        return $this->hasOne(Folio::class, 'ID_Solicitud', 'ID_Solicitud');
    }

    /**
     * Datos descriptivos adicionales adjuntos a la solicitud.
     */
    public function datosSolicitud(): HasOne
    {
        return $this->hasOne(DatoSolicitud::class, 'ID_Solicitud', 'ID_Solicitud');
    }

    /**
     * Documentos o expedientes cargados por el ciudadano para esta solicitud.
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'ID_Solicitud', 'ID_Solicitud');
    }

    /**
     * Asignaciones periciales o de revisión técnica para este expediente.
     */
    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionRevisor::class, 'ID_Solicitud', 'ID_Solicitud');
    }

    /**
     * Citas presenciales o virtuales vinculadas a este expediente.
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'ID_Solicitud', 'ID_Solicitud');
    }

    /**
     * Resoluciones formales o dictámenes finales emitidos.
     */
    public function resoluciones(): HasMany
    {
        return $this->hasMany(Resolucion::class, 'ID_Solicitud', 'ID_Solicitud');
    }
}
