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
        string $hora,
        ?Usuario $revisor = null
    ): Cita {
        return DB::transaction(function () use ($usuario, $solicitud, $fecha, $hora, $revisor): Cita {
            $fechaObj = $fecha instanceof Carbon ? $fecha : Carbon::parse($fecha);
            $fechaString = $fechaObj->toDateString();

            // Normalizar hora de la nueva cita (ej: 09:30:00)
            $horaInicioCandidata = Carbon::parse($fechaString.' '.$hora);

            // Regla: No permitir agendar citas en fechas u horas pasadas
            if ($horaInicioCandidata->isPast()) {
                throw new DomainException('No se pueden agendar citas en fechas u horas pasadas.');
            }

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

            // Regla: Asignar automáticamente el revisor que evaluó y aprobó los documentos del expediente
            if ($revisor === null && $solicitud !== null) {
                $revisor = $solicitud->revisorAprobador();
            }

            // Ventana total por cita = 60 min de atención + 30 min de margen = 90 minutos
            $ventanaTotalMinutos = self::DURACION_MINUTOS + self::MARGEN_MINUTOS;

            // 1. Validar que el ciudadano no tenga colisión de horario consigo mismo
            $citasCiudadano = Cita::query()
                ->where('ID_Usuarios', $usuario->ID_Usuarios)
                ->whereDate('Fecha', $fechaString)
                ->where('Estado_Citas', '!=', 'Cancelada')
                ->lockForUpdate()
                ->get();

            foreach ($citasCiudadano as $existente) {
                $horaInicioExistente = Carbon::parse($fechaString.' '.$existente->Hora);
                $diferenciaMinutos = abs($horaInicioCandidata->diffInMinutes($horaInicioExistente, false));

                if ($diferenciaMinutos < $ventanaTotalMinutos) {
                    throw new DomainException(sprintf(
                        'Horario no disponible: ya cuentas con una cita programada a las %s.',
                        substr((string) $existente->Hora, 0, 5)
                    ));
                }
            }

            // 2. Si hay revisor asignado, validar que ese revisor específico no tenga colisión
            if ($revisor !== null) {
                $citasRevisor = Cita::query()
                    ->where('ID_Revisor', $revisor->ID_Usuarios)
                    ->whereDate('Fecha', $fechaString)
                    ->where('Estado_Citas', '!=', 'Cancelada')
                    ->lockForUpdate()
                    ->get();

                foreach ($citasRevisor as $existente) {
                    $horaInicioExistente = Carbon::parse($fechaString.' '.$existente->Hora);
                    $diferenciaMinutos = abs($horaInicioCandidata->diffInMinutes($horaInicioExistente, false));

                    if ($diferenciaMinutos < $ventanaTotalMinutos) {
                        throw new DomainException(sprintf(
                            'El especialista técnico %s ya cuenta con una cita asignada a las %s. Cada cita dura %d hora y requiere %d minutos de separación.',
                            $revisor->Nombre,
                            substr((string) $existente->Hora, 0, 5),
                            self::DURACION_MINUTOS / 60,
                            self::MARGEN_MINUTOS
                        ));
                    }
                }
            } else {
                // Si no hay revisor asignado, aplicar restricción general sobre citas sin revisor
                $citasGenerales = Cita::query()
                    ->whereNull('ID_Revisor')
                    ->whereDate('Fecha', $fechaString)
                    ->where('Estado_Citas', '!=', 'Cancelada')
                    ->lockForUpdate()
                    ->get();

                foreach ($citasGenerales as $existente) {
                    $horaInicioExistente = Carbon::parse($fechaString.' '.$existente->Hora);
                    $diferenciaMinutos = abs($horaInicioCandidata->diffInMinutes($horaInicioExistente, false));

                    if ($diferenciaMinutos < $ventanaTotalMinutos) {
                        throw new DomainException(sprintf(
                            'Horario no disponible: ya existe una cita general programada a las %s.',
                            substr((string) $existente->Hora, 0, 5)
                        ));
                    }
                }
            }

            $cita = Cita::create([
                'ID_Usuarios' => $usuario->ID_Usuarios,
                'ID_Solicitud' => $solicitud?->ID_Solicitud,
                'ID_Revisor' => $revisor?->ID_Usuarios,
                'Fecha' => $fechaString,
                'Hora' => $horaInicioCandidata->format('H:i:s'),
                'Estado_Citas' => 'Programada',
            ]);

            // Regla: Auditoría y Bitácora obligatoria
            Bitacora::create([
                'ID_Usuarios' => $usuario->ID_Usuarios,
                'Accion' => 'Agendamiento de Cita',
                'Descripcion' => sprintf(
                    'Cita #%d agendada para el %s a las %s con el revisor: %s.',
                    $cita->ID_Citas,
                    $fechaString,
                    $cita->Hora,
                    $revisor?->Nombre ?? 'Asignación automática'
                ),
                'Fecha_Creacion' => now(),
            ]);

            return $cita;
        });
    }

    /**
     * Cancela automáticamente las citas pasadas que sigan en estado 'Programada'.
     * Regla: Las citas vencidas se cancelan automáticamente después de su fecha/hora.
     */
    public function cancelarCitasVencidas(): int
    {
        $hoy = today()->toDateString();
        $horaActual = now()->format('H:i:s');

        // Seleccionar citas vencidas para auditar en bitácora
        $citasVencidas = Cita::where('Estado_Citas', 'Programada')
            ->where(function ($query) use ($hoy, $horaActual): void {
                $query->whereDate('Fecha', '<', $hoy)
                    ->orWhere(function ($q) use ($hoy, $horaActual): void {
                        $q->whereDate('Fecha', '=', $hoy)
                            ->where('Hora', '<', $horaActual);
                    });
            })
            ->get();

        if ($citasVencidas->isEmpty()) {
            return 0;
        }

        $ids = $citasVencidas->pluck('ID_Citas')->all();

        $afectadas = Cita::whereIn('ID_Citas', $ids)
            ->update([
                'Estado_Citas' => 'Cancelada',
                'updated_at' => now(),
            ]);

        foreach ($citasVencidas as $cita) {
            Bitacora::create([
                'ID_Usuarios' => $cita->ID_Usuarios,
                'Accion' => 'Cancelación automática por vencimiento',
                'Descripcion' => sprintf(
                    'La cita #%d programada para el %s a las %s fue cancelada automáticamente por superar la fecha/hora de atención.',
                    $cita->ID_Citas,
                    (string) $cita->Fecha,
                    substr((string) $cita->Hora, 0, 5)
                ),
                'Fecha_Creacion' => now(),
            ]);
        }

        return $afectadas;
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
