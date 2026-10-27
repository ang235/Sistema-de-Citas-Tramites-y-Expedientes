<?php

declare(strict_types=1);

use App\Models\Bitacora;
use App\Models\Cita;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionTramite;
use App\Services\CitaService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('agendarCita valida regla de 1 hora de atencion y 30 minutos de margen entre citas', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Usuario Cita',
        'Email' => 'cita@setc.gob',
        'Contrasena' => 'secret123',
    ]);

    $service = new CitaService;
    $fecha = now()->addDays(3)->toDateString();

    // 1. Cita a las 09:00 (ocupa 09:00 a 10:00 + margen hasta 10:30)
    $cita1 = $service->agendarCita($usuario, null, $fecha, '09:00');
    expect($cita1->Estado_Citas)->toBe('Programada');

    // 2. Colisión durante la atención (09:30) -> debe fallar
    expect(fn () => $service->agendarCita($usuario, null, $fecha, '09:30'))
        ->toThrow(DomainException::class);

    // 3. Colisión en el margen de 30 minutos (10:15) -> debe fallar
    expect(fn () => $service->agendarCita($usuario, null, $fecha, '10:15'))
        ->toThrow(DomainException::class);

    // 4. Cita a las 10:30 (exactamente 90 minutos después) -> debe ser permitida
    $cita2 = $service->agendarCita($usuario, null, $fecha, '10:30');
    expect($cita2->Estado_Citas)->toBe('Programada');

    // Auditoría en bitácora
    expect(Bitacora::where('Accion', 'Agendamiento de Cita')->count())->toBe(2);
});

test('agendarCita impide vincular una solicitud de otro ciudadano', function () {
    $titular = Usuario::create(['Nombre' => 'Titular', 'Email' => 't@setc.gob', 'Contrasena' => '123']);
    $ajeno = Usuario::create(['Nombre' => 'Ajeno', 'Email' => 'a@setc.gob', 'Contrasena' => '123']);

    $tipo = TipoTramite::create(['Nombre' => 'Tramite']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $solicitud = Solicitud::create(['ID_Solicitante' => $titular->ID_Usuarios, 'ID_Version_Tramite' => $version->ID_Version_Tramite]);

    $service = new CitaService;
    $fecha = now()->addDays(5)->toDateString();

    expect(fn () => $service->agendarCita($ajeno, $solicitud, $fecha, '11:00'))
        ->toThrow(DomainException::class);
});

test('cancelarCita actualiza estado y registra auditoria en bitacora', function () {
    $usuario = Usuario::create(['Nombre' => 'Pedro', 'Email' => 'pedro@setc.gob', 'Contrasena' => '123']);
    $service = new CitaService;

    $fecha = now()->addDays(4)->toDateString();
    $cita = $service->agendarCita($usuario, null, $fecha, '14:00');

    $citaCancelada = $service->cancelarCita($cita, $usuario, 'Motivo de fuerza mayor');

    expect($citaCancelada->Estado_Citas)->toBe('Cancelada');
    expect(Bitacora::where('Accion', 'Cancelación de Cita')->exists())->toBeTrue();
});
