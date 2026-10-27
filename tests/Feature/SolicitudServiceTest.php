<?php

declare(strict_types=1);

use App\Models\Bitacora;
use App\Models\Requisito;
use App\Models\Rol;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionTramite;
use App\Services\SolicitudService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('crearSolicitud genera folio unico consecutivo SETC-YYYY-NNNNN e inicializa requisitos en bitacora', function () {
    $ciudadano = Usuario::create([
        'Nombre' => 'Ciudadano Prueba',
        'Email' => 'prueba@setc.gob',
        'Contrasena' => 'password123',
    ]);

    $tipoTramite = TipoTramite::create([
        'Nombre' => 'Licencia de Construcción',
        'Estado_Tipo_Tr' => 'Activo',
    ]);

    $version = VersionTramite::create([
        'ID_Tipo_Tramite' => $tipoTramite->ID_Tipo_Tramite,
        'No_Version' => 'v1.0',
        'Estado_Ver_Tr' => 'Vigente',
    ]);

    Requisito::create([
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Nombre' => 'Plano Arquitectónico',
        'Prioridad' => 'Obligatorio',
    ]);

    Requisito::create([
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Nombre' => 'Memoria Descriptiva',
        'Prioridad' => 'Obligatorio',
    ]);

    $service = new SolicitudService;
    $solicitud1 = $service->crearSolicitud($ciudadano, $version->ID_Version_Tramite, 'Primera solicitud para predio');
    $solicitud2 = $service->crearSolicitud($ciudadano, $version->ID_Version_Tramite, 'Segunda solicitud para local');

    $year = date('Y');
    expect($solicitud1->folio->Codigo_Folio)->toBe("SETC-{$year}-00001");
    expect($solicitud2->folio->Codigo_Folio)->toBe("SETC-{$year}-00002");

    // Valida inicialización de requisitos
    expect($solicitud1->documentos()->count())->toBe(2);
    expect($solicitud1->datosSolicitud->Descripcion_Sol)->toBe('Primera solicitud para predio');

    // Valida auditoría en bitácora
    expect(Bitacora::where('Accion', 'Creación de Expediente')->count())->toBe(2);
});

test('consultarExpediente permite acceso al solicitante titular y rechaza a otro ciudadano', function () {
    $titular = Usuario::create([
        'Nombre' => 'Titular Expediente',
        'Email' => 'titular@setc.gob',
        'Contrasena' => 'password123',
    ]);

    $intruso = Usuario::create([
        'Nombre' => 'Otro Ciudadano',
        'Email' => 'intruso@setc.gob',
        'Contrasena' => 'password123',
    ]);

    $tipoTramite = TipoTramite::create([
        'Nombre' => 'Defensa Civil',
        'Estado_Tipo_Tr' => 'Activo',
    ]);

    $version = VersionTramite::create([
        'ID_Tipo_Tramite' => $tipoTramite->ID_Tipo_Tramite,
        'No_Version' => 'v1.0',
    ]);

    $service = new SolicitudService;
    $solicitud = $service->crearSolicitud($titular, $version->ID_Version_Tramite);

    // Titular puede consultar
    $expediente = $service->consultarExpediente($solicitud, $titular);
    expect($expediente['folio'])->toBe($solicitud->folio->Codigo_Folio);

    // Intruso es rechazado por regla de aislamiento
    expect(fn () => $service->consultarExpediente($solicitud, $intruso))
        ->toThrow(AuthorizationException::class);
});

test('consultarExpediente permite acceso a personal con rol de Supervisor o Administrador', function () {
    $titular = Usuario::create([
        'Nombre' => 'Titular',
        'Email' => 'titular2@setc.gob',
        'Contrasena' => 'pass123',
    ]);

    $admin = Usuario::create([
        'Nombre' => 'Administrador Central',
        'Email' => 'admin@setc.gob',
        'Contrasena' => 'pass123',
    ]);

    $rolAdmin = Rol::create(['Nombre' => 'Administrador']);
    $admin->roles()->attach($rolAdmin->ID_Rol);

    $tipoTramite = TipoTramite::create(['Nombre' => 'Certificado']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipoTramite->ID_Tipo_Tramite, 'No_Version' => 'v1']);

    $service = new SolicitudService;
    $solicitud = $service->crearSolicitud($titular, $version->ID_Version_Tramite);

    $resultado = $service->consultarExpediente($solicitud, $admin);
    expect($resultado['folio'])->toBe($solicitud->folio->Codigo_Folio);
});
