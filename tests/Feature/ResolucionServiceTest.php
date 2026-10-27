<?php

declare(strict_types=1);

use App\Models\Bitacora;
use App\Models\Rol;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionTramite;
use App\Services\ResolucionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('emitirResolucion permite a supervisor emitir resolucion final y registra auditoria en bitacora', function () {
    $solicitante = Usuario::create(['Nombre' => 'Ciudadano', 'Email' => 'sol@setc.gob', 'Contrasena' => '123']);
    $supervisor = Usuario::create(['Nombre' => 'Supervisor Jefe', 'Email' => 'jefe@setc.gob', 'Contrasena' => '123']);

    $rolSupervisor = Rol::create(['Nombre' => 'Supervisor']);
    $supervisor->roles()->attach($rolSupervisor->ID_Rol);

    $tipo = TipoTramite::create(['Nombre' => 'Habilitación Urbana']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1.0']);
    $solicitud = Solicitud::create(['ID_Solicitante' => $solicitante->ID_Usuarios, 'ID_Version_Tramite' => $version->ID_Version_Tramite]);

    $service = new ResolucionService;
    $resolucion = $service->emitirResolucion($solicitud, $supervisor, 'Aprobada');

    expect($resolucion->Tipo_Resolucion)->toBe('Aprobada');
    expect($resolucion->ID_Supervisor)->toBe($supervisor->ID_Usuarios);
    expect($resolucion->ID_Solicitud)->toBe($solicitud->ID_Solicitud);

    // Valida auditoría en bitácora
    expect(Bitacora::where('Accion', 'Emisión de Resolución')->exists())->toBeTrue();
});

test('emitirResolucion rechaza a usuario sin rol de supervisor o administrador', function () {
    $ciudadano = Usuario::create(['Nombre' => 'Ciudadano Comun', 'Email' => 'comun@setc.gob', 'Contrasena' => '123']);
    $tipo = TipoTramite::create(['Nombre' => 'Tramite Z']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $solicitud = Solicitud::create(['ID_Solicitante' => $ciudadano->ID_Usuarios, 'ID_Version_Tramite' => $version->ID_Version_Tramite]);

    $service = new ResolucionService;

    expect(fn () => $service->emitirResolucion($solicitud, $ciudadano, 'Aprobada'))
        ->toThrow(AuthorizationException::class);
});

test('emitirResolucion rechaza tipos de resolucion no permitidos', function () {
    $supervisor = Usuario::create(['Nombre' => 'Supervisor', 'Email' => 'sup2@setc.gob', 'Contrasena' => '123']);
    $rolSupervisor = Rol::create(['Nombre' => 'Supervisor']);
    $supervisor->roles()->attach($rolSupervisor->ID_Rol);

    $tipo = TipoTramite::create(['Nombre' => 'Tramite W']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $solicitud = Solicitud::create(['ID_Solicitante' => $supervisor->ID_Usuarios, 'ID_Version_Tramite' => $version->ID_Version_Tramite]);

    $service = new ResolucionService;

    expect(fn () => $service->emitirResolucion($solicitud, $supervisor, 'TipoInvalido'))
        ->toThrow(DomainException::class);
});
