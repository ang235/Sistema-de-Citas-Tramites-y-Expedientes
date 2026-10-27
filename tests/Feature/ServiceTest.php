<?php

declare(strict_types=1);

use App\Models\Bitacora;
use App\Models\Cita;
use App\Models\Documento;
use App\Models\Requisito;
use App\Models\Rol;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionTramite;
use App\Services\CitaService;
use App\Services\DocumentoService;
use App\Services\ResolucionService;
use App\Services\SolicitudService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('solicitud service generates sequential and unique folio format SETC-YYYY-NNNNN', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Ejemplo',
        'Email' => 'ciudadano@setc.gob',
        'Contrasena' => 'secret123',
    ]);

    $tipoTramite = TipoTramite::create([
        'Nombre' => 'Licencia de Funcionamiento',
        'Descripcion' => 'Trámite municipal',
        'Estado_Tipo_Tr' => 'Activo',
    ]);

    $version = VersionTramite::create([
        'ID_Tipo_Tramite' => $tipoTramite->ID_Tipo_Tramite,
        'No_Version' => 'v1.0',
        'Estado_Ver_Tr' => 'Vigente',
    ]);

    $service = new SolicitudService;
    $solicitud1 = $service->crearSolicitud($usuario, $version->ID_Version_Tramite, 'Primera solicitud');
    $solicitud2 = $service->crearSolicitud($usuario, $version->ID_Version_Tramite, 'Segunda solicitud');

    $year = date('Y');
    expect($solicitud1->folio->Codigo_Folio)->toBe("SETC-{$year}-00001");
    expect($solicitud2->folio->Codigo_Folio)->toBe("SETC-{$year}-00002");
});

test('cita service enforces 1 hour duration and 30 minutes separation rule', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Carlos Gomez',
        'Email' => 'carlos@setc.gob',
        'Contrasena' => 'secret123',
    ]);

    $service = new CitaService;
    $fecha = now()->addDays(2)->toDateString();

    // 1. Agendar cita inicial a las 10:00 (ocupa 10:00 a 11:00 + margen hasta 11:30)
    $cita1 = $service->agendarCita($usuario, null, $fecha, '10:00');
    expect($cita1->Estado_Citas)->toBe('Programada');

    // 2. Intentar agendar a las 10:45 (colisiona porque la primera aún está en atención)
    expect(fn () => $service->agendarCita($usuario, null, $fecha, '10:45'))
        ->toThrow(DomainException::class);

    // 3. Intentar agendar a las 11:15 (colisiona dentro del margen de 30 minutos de separación)
    expect(fn () => $service->agendarCita($usuario, null, $fecha, '11:15'))
        ->toThrow(DomainException::class);

    // 4. Agendar a las 11:30 (permitida: 1 hora de atención + 30 min de margen transcurridos)
    $cita2 = $service->agendarCita($usuario, null, $fecha, '11:30');
    expect($cita2->Estado_Citas)->toBe('Programada');
});

test('documento service generates new versions without overwriting and registers in bitacora', function () {
    Storage::fake('local');

    $usuario = Usuario::create([
        'Nombre' => 'Mariana Ruiz',
        'Email' => 'mariana@setc.gob',
        'Contrasena' => 'secret123',
    ]);

    $tipoTramite = TipoTramite::create([
        'Nombre' => 'Permiso Ambiental',
        'Estado_Tipo_Tr' => 'Activo',
    ]);

    $versionTramite = VersionTramite::create([
        'ID_Tipo_Tramite' => $tipoTramite->ID_Tipo_Tramite,
        'No_Version' => 'v1.0',
    ]);

    $requisito = Requisito::create([
        'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
        'Nombre' => 'Memoria Técnica',
        'Prioridad' => 'Obligatorio',
    ]);

    $solicitud = Solicitud::create([
        'ID_Solicitante' => $usuario->ID_Usuarios,
        'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
    ]);

    $documento = Documento::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Requisitos' => $requisito->ID_Requisitos,
        'Estado_Doc' => 'Pendiente',
    ]);

    $service = new DocumentoService;

    // Subir primera versión
    $archivo1 = UploadedFile::fake()->create('memoria_v1.pdf', 500, 'application/pdf');
    $v1 = $service->subirNuevaVersion($documento, $archivo1, $usuario);

    // Subir segunda versión (subsanación sin sobreescribir)
    $archivo2 = UploadedFile::fake()->create('memoria_v2.pdf', 600, 'application/pdf');
    $v2 = $service->subirNuevaVersion($documento, $archivo2, $usuario);

    expect($documento->versionesDoc()->count())->toBe(2);
    expect($v1->ID_Version_Doc)->not->toBe($v2->ID_Version_Doc);

    // Verificar registro en bitácora
    expect(Bitacora::where('Accion', 'Actualización de Documento')->count())->toBe(2);
});

test('resolucion service emits resolution and registers audit in bitacora', function () {
    $ciudadano = Usuario::create([
        'Nombre' => 'Solicitante Test',
        'Email' => 'solicitante@setc.gob',
        'Contrasena' => 'secret123',
    ]);

    $supervisor = Usuario::create([
        'Nombre' => 'Supervisor Test',
        'Email' => 'supervisor@setc.gob',
        'Contrasena' => 'secret123',
    ]);

    $rolSupervisor = Rol::create([
        'Nombre' => 'Supervisor',
    ]);
    $supervisor->roles()->attach($rolSupervisor->ID_Rol);

    $tipoTramite = TipoTramite::create([
        'Nombre' => 'Uso de Suelo',
    ]);

    $versionTramite = VersionTramite::create([
        'ID_Tipo_Tramite' => $tipoTramite->ID_Tipo_Tramite,
        'No_Version' => 'v1.0',
    ]);

    $solicitud = Solicitud::create([
        'ID_Solicitante' => $ciudadano->ID_Usuarios,
        'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
    ]);

    $service = new ResolucionService;
    $resolucion = $service->emitirResolucion($solicitud, $supervisor, 'Aprobada');

    expect($resolucion->Tipo_Resolucion)->toBe('Aprobada');
    expect(Bitacora::where('Accion', 'Emisión de Resolución')->exists())->toBeTrue();
});
