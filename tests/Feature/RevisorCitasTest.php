<?php

declare(strict_types=1);

use App\Models\Cita;
use App\Models\Rol;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionTramite;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('revisor técnico puede ver sus citas pendientes con detalles en el dashboard de revisor', function () {
    $rolRevisor = Rol::firstOrCreate(['Nombre' => 'Revisor']);

    $revisor = Usuario::create([
        'Nombre' => 'Ing. Revisor Uno',
        'Email' => 'revisor1@setc.gob',
        'Contrasena' => 'secret123',
    ]);
    $revisor->roles()->attach($rolRevisor->ID_Rol);
    $revisor->load('roles');

    $ciudadano = Usuario::create([
        'Nombre' => 'Ciudadano Juan Pérez',
        'Email' => 'juan.perez@ciudadano.gob',
        'Contrasena' => 'secret123',
    ]);

    $tipo = TipoTramite::create(['Nombre' => 'Licencia de Funcionamiento ITSE', 'Descripcion' => 'ITSE', 'Activo' => true]);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 1, 'Estado_Ver_Tr' => 'Vigente']);
    $solicitud = Solicitud::create([
        'ID_Solicitante' => $ciudadano->ID_Usuarios,
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Estado_Solicitud' => 'En_Revision',
    ]);

    $cita = Cita::create([
        'ID_Usuarios' => $ciudadano->ID_Usuarios,
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Revisor' => $revisor->ID_Usuarios,
        'Fecha' => now()->addDays(2)->toDateString(),
        'Hora' => '10:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    $response = $this->actingAs($revisor)->get(route('portal.revisor-dashboard'));

    $response->assertOk();
    $response->assertSee('Citas Técnicas Pendientes de Atención');
    $response->assertSee('Ciudadano Juan Pérez');
    $response->assertSee('10:00 hrs');
    $response->assertViewHas('citasPendientes', function ($citas) use ($cita) {
        return $citas->contains('ID_Citas', $cita->ID_Citas);
    });
});

test('revisor técnico puede ver la agenda de citas filtrada a sus citas asignadas', function () {
    $rolRevisor = Rol::firstOrCreate(['Nombre' => 'Revisor']);

    $revisorA = Usuario::create([
        'Nombre' => 'Revisor A',
        'Email' => 'revisora@setc.gob',
        'Contrasena' => 'secret123',
    ]);
    $revisorA->roles()->attach($rolRevisor->ID_Rol);
    $revisorA->load('roles');

    $revisorB = Usuario::create([
        'Nombre' => 'Revisor B',
        'Email' => 'revisorb@setc.gob',
        'Contrasena' => 'secret123',
    ]);
    $revisorB->roles()->attach($rolRevisor->ID_Rol);
    $revisorB->load('roles');

    $ciudadanoA = Usuario::create(['Nombre' => 'Carlos Solicitante A', 'Email' => 'ca@test.gob', 'Contrasena' => '123']);
    $ciudadanoB = Usuario::create(['Nombre' => 'María Solicitante B', 'Email' => 'mb@test.gob', 'Contrasena' => '123']);

    $citaA = Cita::create([
        'ID_Usuarios' => $ciudadanoA->ID_Usuarios,
        'ID_Revisor' => $revisorA->ID_Usuarios,
        'Fecha' => now()->addDays(1)->toDateString(),
        'Hora' => '09:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    $citaB = Cita::create([
        'ID_Usuarios' => $ciudadanoB->ID_Usuarios,
        'ID_Revisor' => $revisorB->ID_Usuarios,
        'Fecha' => now()->addDays(1)->toDateString(),
        'Hora' => '09:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    $response = $this->actingAs($revisorA)->get(route('portal.citas'));

    $response->assertOk();
    $response->assertSee('Mis Citas Asignadas en Ventanilla');
    $response->assertSee('Carlos Solicitante A');
    $response->assertDontSee('María Solicitante B');
});

test('revisor puede marcar como atendida su cita pero no la de otro revisor', function () {
    $rolRevisor = Rol::firstOrCreate(['Nombre' => 'Revisor']);

    $revisorA = Usuario::create(['Nombre' => 'Revisor A', 'Email' => 'reva@setc.gob', 'Contrasena' => '123']);
    $revisorA->roles()->attach($rolRevisor->ID_Rol);
    $revisorA->load('roles');

    $revisorB = Usuario::create(['Nombre' => 'Revisor B', 'Email' => 'revb@setc.gob', 'Contrasena' => '123']);
    $revisorB->roles()->attach($rolRevisor->ID_Rol);
    $revisorB->load('roles');

    $ciudadano = Usuario::create(['Nombre' => 'Ciudadano Test', 'Email' => 'ct@test.gob', 'Contrasena' => '123']);

    $citaA = Cita::create([
        'ID_Usuarios' => $ciudadano->ID_Usuarios,
        'ID_Revisor' => $revisorA->ID_Usuarios,
        'Fecha' => now()->addDays(1)->toDateString(),
        'Hora' => '10:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    $citaB = Cita::create([
        'ID_Usuarios' => $ciudadano->ID_Usuarios,
        'ID_Revisor' => $revisorB->ID_Usuarios,
        'Fecha' => now()->addDays(1)->toDateString(),
        'Hora' => '11:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    // 1. Revisor A actualiza su propia cita -> permitido
    $resA = $this->actingAs($revisorA)->patch(route('portal.citas.estado', $citaA->ID_Citas), [
        'estado_citas' => 'Atendida',
    ]);
    $resA->assertRedirect();
    expect($citaA->fresh()->Estado_Citas)->toBe('Atendida');

    // 2. Revisor A intenta actualizar la cita de Revisor B -> 403 Forbidden
    $resB = $this->actingAs($revisorA)->patch(route('portal.citas.estado', $citaB->ID_Citas), [
        'estado_citas' => 'Atendida',
    ]);
    $resB->assertForbidden();
    expect($citaB->fresh()->Estado_Citas)->toBe('Programada');
});

test('supervisor no puede modificar el estado de ninguna cita pero sí puede verlas en modo solo lectura', function () {
    $rolSupervisor = Rol::firstOrCreate(['Nombre' => 'Supervisor']);
    $rolRevisor = Rol::firstOrCreate(['Nombre' => 'Revisor']);

    $supervisor = Usuario::create(['Nombre' => 'Supervisor Dra. Silva', 'Email' => 'super@setc.gob', 'Contrasena' => '123']);
    $supervisor->roles()->attach($rolSupervisor->ID_Rol);
    $supervisor->load('roles');

    $revisor = Usuario::create(['Nombre' => 'Revisor Gómez', 'Email' => 'revisorg@setc.gob', 'Contrasena' => '123']);
    $revisor->roles()->attach($rolRevisor->ID_Rol);
    $revisor->load('roles');

    $ciudadano = Usuario::create(['Nombre' => 'Ciudadano Test', 'Email' => 'ciud@test.gob', 'Contrasena' => '123']);

    $cita = Cita::create([
        'ID_Usuarios' => $ciudadano->ID_Usuarios,
        'ID_Revisor' => $revisor->ID_Usuarios,
        'Fecha' => now()->addDays(1)->toDateString(),
        'Hora' => '10:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    // 1. Supervisor ve la agenda en modo solo lectura
    $response = $this->actingAs($supervisor)->get(route('portal.citas'));
    $response->assertOk();
    $response->assertSee('Supervisión Institucional de Citas (Solo Lectura)');

    // 2. Supervisor intenta modificar el estado de la cita -> 403 Forbidden
    $patchRes = $this->actingAs($supervisor)->patch(route('portal.citas.estado', $cita->ID_Citas), [
        'estado_citas' => 'Atendida',
    ]);
    $patchRes->assertForbidden();
    expect($cita->fresh()->Estado_Citas)->toBe('Programada');
});
