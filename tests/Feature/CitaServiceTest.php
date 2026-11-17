<?php

declare(strict_types=1);

use App\Models\Bitacora;
use App\Models\Cita;
use App\Models\Rol;
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

test('agendarCita rechaza fechas u horas en el pasado', function () {
    $usuario = Usuario::create(['Nombre' => 'Test', 'Email' => 'test_pasado@setc.gob', 'Contrasena' => '123']);
    $service = new CitaService;

    // Fecha en el pasado (hace 2 días)
    $fechaPasada = now()->subDays(2)->toDateString();

    expect(fn () => $service->agendarCita($usuario, null, $fechaPasada, '10:00'))
        ->toThrow(DomainException::class, 'No se pueden agendar citas en fechas u horas pasadas.');
});

test('HTTP endpoint citas.store rechaza citas en fechas anteriores y horarios duplicados', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Cita',
        'Email' => 'ciudadano_cita@setc.gob',
        'Contrasena' => bcrypt('password123'),
    ]);

    $otroCiudadano = Usuario::create([
        'Nombre' => 'Segundo Ciudadano',
        'Email' => 'segundo_cita@setc.gob',
        'Contrasena' => bcrypt('password123'),
    ]);

    // Asignar rol ciudadano
    $rolCiudadano = Rol::firstOrCreate(['Nombre' => 'Ciudadano']);
    $usuario->roles()->attach($rolCiudadano->ID_Rol);
    $otroCiudadano->roles()->attach($rolCiudadano->ID_Rol);

    $tipo = TipoTramite::create(['Nombre' => 'Licencia de Obra']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $solicitud1 = Solicitud::create([
        'ID_Solicitante' => $usuario->ID_Usuarios,
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Estado_Solicitud' => 'Aprobado',
    ]);
    $solicitud2 = Solicitud::create([
        'ID_Solicitante' => $otroCiudadano->ID_Usuarios,
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Estado_Solicitud' => 'Aprobado',
    ]);

    // 1. Intentar fecha pasada
    $fechaPasada = now()->subDay()->toDateString();
    $response = $this->actingAs($usuario)->post(route('citas.store'), [
        'id_solicitud' => $solicitud1->ID_Solicitud,
        'fecha' => $fechaPasada,
        'hora' => '09:30',
    ]);
    $response->assertSessionHasErrors(['fecha']);

    // 2. Agendar cita válida
    $fechaFutura = now()->addDays(10)->toDateString();
    $responseOk = $this->actingAs($usuario)->post(route('citas.store'), [
        'id_solicitud' => $solicitud1->ID_Solicitud,
        'fecha' => $fechaFutura,
        'hora' => '10:00',
    ]);
    $responseOk->assertSessionHasNoErrors();
    expect(Cita::whereDate('Fecha', $fechaFutura)->where('Hora', '10:00')->exists())->toBeTrue();

    // 3. Intentar agendar exactamente el mismo horario y fecha por otro usuario (duplicado)
    $responseDup = $this->actingAs($otroCiudadano)->post(route('citas.store'), [
        'id_solicitud' => $solicitud2->ID_Solicitud,
        'fecha' => $fechaFutura,
        'hora' => '10:00',
    ]);
    $responseDup->assertSessionHasErrors(['hora']);
});

test('citas.store exige que los documentos esten aprobados por el revisor antes de agendar', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Sin Aprobacion',
        'Email' => 'sin_aprobacion@setc.gob',
        'Contrasena' => bcrypt('password123'),
    ]);
    $rolCiudadano = Rol::firstOrCreate(['Nombre' => 'Ciudadano']);
    $usuario->roles()->attach($rolCiudadano->ID_Rol);

    $tipo = TipoTramite::create(['Nombre' => 'Constancia']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $solicitudPendiente = Solicitud::create([
        'ID_Solicitante' => $usuario->ID_Usuarios,
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Estado_Solicitud' => 'En Revisión',
    ]);

    $fechaFutura = now()->addDays(5)->toDateString();
    $response = $this->actingAs($usuario)->post(route('citas.store'), [
        'id_solicitud' => $solicitudPendiente->ID_Solicitud,
        'fecha' => $fechaFutura,
        'hora' => '10:00',
    ]);

    $response->assertSessionHasErrors(['id_solicitud']);
    expect(Cita::where('ID_Solicitud', $solicitudPendiente->ID_Solicitud)->exists())->toBeFalse();
});

test('citas.store solo permite una cita activa a la vez para el mismo usuario', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Una Cita',
        'Email' => 'una_cita@setc.gob',
        'Contrasena' => bcrypt('password123'),
    ]);
    $rolCiudadano = Rol::firstOrCreate(['Nombre' => 'Ciudadano']);
    $usuario->roles()->attach($rolCiudadano->ID_Rol);

    $tipo = TipoTramite::create(['Nombre' => 'Permiso Especial']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $solicitudAprobada = Solicitud::create([
        'ID_Solicitante' => $usuario->ID_Usuarios,
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Estado_Solicitud' => 'Aprobado',
    ]);

    // Crear primera cita activa programada
    Cita::create([
        'ID_Usuarios' => $usuario->ID_Usuarios,
        'ID_Solicitud' => $solicitudAprobada->ID_Solicitud,
        'Fecha' => now()->addDays(3)->toDateString(),
        'Hora' => '09:00',
        'Estado_Citas' => 'Programada',
    ]);

    // Intentar agendar una segunda cita simultánea
    $response = $this->actingAs($usuario)->post(route('citas.store'), [
        'id_solicitud' => $solicitudAprobada->ID_Solicitud,
        'fecha' => now()->addDays(6)->toDateString(),
        'hora' => '11:00',
    ]);

    $response->assertSessionHasErrors(['cita']);
    expect(Cita::where('ID_Usuarios', $usuario->ID_Usuarios)->count())->toBe(1);
});

test('citas.store rechaza citas agendadas fuera del plazo de 15 dias posteriores a aprobacion', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Plazo 15',
        'Email' => 'plazo15@setc.gob',
        'Contrasena' => bcrypt('password123'),
    ]);
    $rolCiudadano = Rol::firstOrCreate(['Nombre' => 'Ciudadano']);
    $usuario->roles()->attach($rolCiudadano->ID_Rol);

    $tipo = TipoTramite::create(['Nombre' => 'Permiso Obra']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $solicitudAprobada = Solicitud::create([
        'ID_Solicitante' => $usuario->ID_Usuarios,
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Estado_Solicitud' => 'Aprobado',
    ]);

    // Fecha más allá de los 15 días permitidos (día 16)
    $fechaExcedida = now()->addDays(16)->toDateString();
    $response = $this->actingAs($usuario)->post(route('citas.store'), [
        'id_solicitud' => $solicitudAprobada->ID_Solicitud,
        'fecha' => $fechaExcedida,
        'hora' => '10:00',
    ]);

    $response->assertSessionHasErrors(['fecha']);
    expect(Cita::where('ID_Solicitud', $solicitudAprobada->ID_Solicitud)->exists())->toBeFalse();
});

test('cancelarCitasVencidas cancela automaticamente citas cuya fecha u hora ha expirado', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Usuario Vencido',
        'Email' => 'vencido@setc.gob',
        'Contrasena' => 'secret123',
    ]);

    // Cita de hace 2 días en estado Programada
    $citaAyer = Cita::create([
        'ID_Usuarios' => $usuario->ID_Usuarios,
        'Fecha' => now()->subDays(2)->toDateString(),
        'Hora' => '09:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    // Cita futura que debe mantenerse Programada
    $citaFutura = Cita::create([
        'ID_Usuarios' => $usuario->ID_Usuarios,
        'Fecha' => now()->addDays(5)->toDateString(),
        'Hora' => '11:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    $service = new CitaService;
    $canceladas = $service->cancelarCitasVencidas();

    expect($canceladas)->toBe(1);
    expect($citaAyer->fresh()->Estado_Citas)->toBe('Cancelada');
    expect($citaFutura->fresh()->Estado_Citas)->toBe('Programada');
    expect(Bitacora::where('Accion', 'Cancelación automática por vencimiento')->exists())->toBeTrue();
});

test('perfil del usuario muestra sus citas agendadas', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Perfil',
        'Email' => 'perfil@setc.gob',
        'Contrasena' => bcrypt('password123'),
    ]);

    $rolCiudadano = Rol::firstOrCreate(['Nombre' => 'Ciudadano']);
    $usuario->roles()->attach($rolCiudadano->ID_Rol);

    $fecha = now()->addDays(14)->toDateString();
    Cita::create([
        'ID_Usuarios' => $usuario->ID_Usuarios,
        'Fecha' => $fecha,
        'Hora' => '10:30:00',
        'Estado_Citas' => 'Programada',
    ]);

    $response = $this->actingAs($usuario)->get(route('profile.show'));
    $response->assertOk();
    $response->assertSee('Mis Citas Agendadas');
    $response->assertSee('10:30');
    $response->assertSee('Programada');
});

test('agenda de citas de administracion muestra todas las citas por defecto', function () {
    $admin = Usuario::create([
        'Nombre' => 'Admin Citas',
        'Email' => 'admin_citas@setc.gob',
        'Contrasena' => bcrypt('admin123'),
    ]);
    $rolAdmin = Rol::firstOrCreate(['Nombre' => 'Administrador']);
    $admin->roles()->attach($rolAdmin->ID_Rol);

    $ciudadano = Usuario::create([
        'Nombre' => 'Ciudadano Uno',
        'Email' => 'c1@setc.gob',
        'Contrasena' => '123',
    ]);

    // Cita pasada atendida
    Cita::create([
        'ID_Usuarios' => $ciudadano->ID_Usuarios,
        'Fecha' => now()->subDays(5)->toDateString(),
        'Hora' => '10:00:00',
        'Estado_Citas' => 'Atendida',
    ]);

    // Cita futura
    Cita::create([
        'ID_Usuarios' => $ciudadano->ID_Usuarios,
        'Fecha' => now()->addDays(7)->toDateString(),
        'Hora' => '11:00:00',
        'Estado_Citas' => 'Programada',
    ]);

    $response = $this->actingAs($admin)->get(route('portal.citas'));
    $response->assertOk();
    $response->assertSee('Todas las Citas Registradas');
    $response->assertSee('Atendida');
    $response->assertSee('Ciudadano Uno');
});
