<?php

declare(strict_types=1);

use App\Models\Bitacora;
use App\Models\Requisito;
use App\Models\Rol;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionTramite;
use App\Services\SolicitudService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

test('nuevo usuario registrado puede iniciar y registrar un tramite exitosamente', function () {
    // 1. Registro de nuevo usuario
    $registroResponse = $this->post(route('register'), [
        'nombre' => 'Nuevo Ciudadano',
        'email' => 'nuevo_ciudadano@setc.gob',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $registroResponse->assertRedirect();

    $usuario = Usuario::where('Email', 'nuevo_ciudadano@setc.gob')->first();
    expect($usuario)->not->toBeNull();
    expect($usuario->esCiudadano())->toBeTrue();

    // 2. Configurar trámite con versión y requisito
    $tipoTramite = TipoTramite::create([
        'Nombre' => 'Trámite Telemático Nuevo',
        'Estado_Tipo_Tr' => 'Activo',
    ]);
    $version = VersionTramite::create([
        'ID_Tipo_Tramite' => $tipoTramite->ID_Tipo_Tramite,
        'No_Version' => 'v1.0',
        'Estado_Ver_Tr' => 'Vigente',
    ]);
    $requisito = Requisito::create([
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Nombre' => 'Identificación Oficial',
        'Prioridad' => 'Obligatorio',
    ]);

    // 3. Ver formulario de inicio de trámite
    $formResponse = $this->actingAs($usuario)->get(route('solicitudes.create', $tipoTramite->ID_Tipo_Tramite));
    $formResponse->assertOk();
    $formResponse->assertSee('Trámite Telemático Nuevo');

    // 4. Enviar solicitud con documento PDF
    Storage::fake('local');
    $fakePdf = UploadedFile::fake()->create('ine.pdf', 500, 'application/pdf');

    $storeResponse = $this->actingAs($usuario)->post(route('solicitudes.store'), [
        'id_version_tramite' => $version->ID_Version_Tramite,
        'descripcion' => 'Apertura de expediente por ciudadano recién registrado',
        'documentos' => [
            $requisito->ID_Requisitos => $fakePdf,
        ],
    ]);

    $storeResponse->assertRedirect();
    expect(Solicitud::where('ID_Solicitante', $usuario->ID_Usuarios)->exists())->toBeTrue();
    $solicitud = Solicitud::where('ID_Solicitante', $usuario->ID_Usuarios)->first();
    expect($solicitud->documentos()->count())->toBe(1);
    expect($solicitud->documentos->first()->versiones()->count())->toBe(1);
});
