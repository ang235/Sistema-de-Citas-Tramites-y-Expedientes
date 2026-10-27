<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $ID_Citas
 * @property int $ID_Usuarios
 * @property int|null $ID_Solicitud
 * @property Carbon $Fecha
 * @property string $Hora
 * @property string $Estado_Citas
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Usuario $usuario
 * @property-read Solicitud|null $solicitud
 */
#[Fillable(['ID_Usuarios', 'ID_Solicitud', 'Fecha', 'Hora', 'Estado_Citas'])]
class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $primaryKey = 'ID_Citas';

    public $incrementing = true;

    protected $keyType = 'int';

    protected function casts(): array
    {
        return [
            'Fecha' => 'date',
        ];
    }

    /**
     * Usuario ciudadano o solicitante agendado en la cita.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ID_Usuarios', 'ID_Usuarios');
    }

    /**
     * Solicitud o trámite asociado opcionalmente a la cita.
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'ID_Solicitud', 'ID_Solicitud');
    }
}
