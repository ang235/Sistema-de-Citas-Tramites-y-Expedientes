<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Bitacora;
use App\Models\Cita;
use App\Models\Solicitud;
use App\Models\Usuario;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class CitaService
{
    /**
     * Duración en minutos de atención por cita.
     */
    public const DURACION_MINUTOS = 60;

    /**
     * Separación mínima requerida entre citas consecutivas (media hora).
     */
    public const MARGEN_MINUTOS = 30;

    /**
     * Agenda una nueva cita validando estrictamente las reglas de tiempo:
     * - Cada cita tiene 1 hora de atención.
     * - Debe existir mínimo media hora (30 minutos) entre una cita y otra.
     *
     * @throws DomainException|\Throwable
     */
    public function agendarCita(
        Usuario $usuario,
        ?Solicitud $solicitud,
        Carbon|string $fecha,
        string $hora
    ): Cita {
        return DB::transaction(function () use ($usuario, $solicitud, $fecha, $hora): Cita {
            $fechaObj = $fecha instanceof Carbon ? $fecha : Carbon::parse($fecha);
            $fechaString = $fechaObj->toDateString();

            // Normalizar hora de la nueva cita (ej: 09:30:00)
            $horaInicioCandidata = Carbon::parse($fechaString.' '.$hora);

            // Regla: Aislamiento del solicitante en caso de vincularse a un expediente
            if ($solicitud !== null && (int) $solicitud->ID_Solicitante !== (int) $usuario->ID_Usuarios) {
                if (! $usuario->relationLoaded('roles')) {
                    $usuario->load('roles');
                }
                $roles = $usuario->roles->pluck('Nombre')->map(fn ($r) => strtolower((string) $r))->all();
                if (! in_array('administrador', $roles, true) && ! in_array('supervisor', $roles, true)) {
                    throw new DomainException('No puede agendar citas para un expediente que no le pertenece.');
                }
            }

            // Consultar citas activas programadas para la misma fecha (del usuario o generales)
            $citasExistentes = Cita::query()
                ->whereDate('Fecha', $fechaString)
                ->where('Estado_Citas', '!=', 'Cancelada')
                ->where(function ($query) use ($usuario) {
                    $query->where('ID_Usuarios', $usuario->ID_Usuarios);
                })
                ->lockForUpdate()
                ->get();

            // Ventana total por cita = 60 min de atención + 30 min de margen = 90 minutos
            $ventanaTotalMinutos = self::DURACION_MINUTOS + self::MARGEN_MINUTOS;

            foreach ($citasExistentes as $existente) {
                $horaInicioExistente = Carbon::parse($fechaString.' '.$existente->Hora);
                $diferenciaMinutos = abs($horaInicioCandidata->diffInMinutes($horaInicioExistente, false));

                if ($diferenciaMinutos < $ventanaTotalMinutos) {
                    throw new DomainException(sprintf(
                        'Horario no disponible: ya existe una cita programada a las %s. Cada cita dura %d hora y requiere mínimo %d minutos de separación.',
                        substr((string) $existente->Hora, 0, 5),
                        self::DURACION_MINUTOS / 60,
                        self::MARGEN_MINUTOS
                    ));
                }
            }

            $cita = Cita::create([
                'ID_Usuarios' => $usuario->ID_Usuarios,
                'ID_Solicitud' => $solicitud?->ID_Solicitud,
                'Fecha' => $fechaString,
                'Hora' => $horaInicioCandidata->format('H:i:s'),
                'Estado_Citas' => 'Programada',
            ]);

            // Regla: Auditoría y Bitácora obligatoria
            Bitacora::create([
                'ID_Usuarios' => $usuario->ID_Usuarios,
                'Accion' => 'Agendamiento de Cita',
                'Descripcion' => sprintf(
                    'Cita #%d agendada para el %s a las %s (1 hora de atención + 30 min de margen).',
                    $cita->ID_Citas,
                    $fechaString,
                    $horaInicioCandidata->format('H:i')
                ),
                'Fecha_Creacion' => now(),
            ]);

            return $cita;
        });
    }

    /**
     * Cancela una cita y registra la justificación en bitácora.
     */
    public function cancelarCita(Cita $cita, Usuario $usuario, string $motivo): Cita
    {
        return DB::transaction(function () use ($cita, $usuario, $motivo): Cita {
            $cita->update([
                'Estado_Citas' => 'Cancelada',
            ]);

            // Regla: Auditoría y Bitácora obligatoria
            Bitacora::create([
                'ID_Usuarios' => $usuario->ID_Usuarios,
                'Accion' => 'Cancelación de Cita',
                'Descripcion' => sprintf(
                    'Cita #%d cancelada por el usuario #%d. Motivo: %s',
                    $cita->ID_Citas,
                    $usuario->ID_Usuarios,
                    $motivo
                ),
                'Fecha_Creacion' => now(),
            ]);

            return $cita;
        });
    }
}
