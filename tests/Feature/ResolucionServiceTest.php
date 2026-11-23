<?php

declare(strict_types=1);

use App\Models\Bitacora;
use App\Models\Folio;
use App\Models\Resolucion;
use App\Models\Rol;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionTramite;
use App\Services\ResolucionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

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
    expect($solicitud->fresh()->Estado_Solicitud)->toBe('Aprobado');

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

test('administrador y supervisor pueden emitir resolucion via controlador portal.resoluciones.store', function () {
    $admin = Usuario::create(['Nombre' => 'Admin Boss', 'Email' => 'admin@setc.gob', 'Contrasena' => bcrypt('password')]);
    $rolAdmin = Rol::create(['Nombre' => 'Administrador']);
    $admin->roles()->attach($rolAdmin->ID_Rol);

    $solicitante = Usuario::create(['Nombre' => 'Ciudadano Solicitante', 'Email' => 'solicitante@test.com', 'Contrasena' => bcrypt('password')]);

    $tipo = TipoTramite::create(['Nombre' => 'Licencia de Negocio']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1.0']);
    $solicitud = Solicitud::create(['ID_Solicitante' => $solicitante->ID_Usuarios, 'ID_Version_Tramite' => $version->ID_Version_Tramite, 'Estado_Solicitud' => 'En Revision']);

    $response = $this->actingAs($admin)->post(route('portal.resoluciones.store', $solicitud->ID_Solicitud), [
        'tipo_resolucion' => 'Favorable',
        'comentarios' => 'Cumple integramente con la normativa de habilitacion comercial.',
    ]);

    $response->assertSessionHas('success');
    expect($solicitud->fresh()->Estado_Solicitud)->toBe('Aprobado');
    expect(Resolucion::where('ID_Solicitud', $solicitud->ID_Solicitud)->exists())->toBeTrue();
    expect(Bitacora::where('Accion', 'Expediente Concluido: Resolución Formal Emitida')->exists())->toBeTrue();
});

test('un ciudadano puede descargar la resolucion por folio oficial', function () {
    Pdf::fake();

    $solicitante = Usuario::create(['Nombre' => 'Ciudadano Solicitante', 'Email' => 'solicitante2@test.com', 'Contrasena' => bcrypt('password')]);
    $rolCiudadano = Rol::create(['Nombre' => 'Ciudadano']);
    $solicitante->roles()->attach($rolCiudadano->ID_Rol);

    $tipo = TipoTramite::create(['Nombre' => 'Certificado de Zonificacion']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1.0']);
    $solicitud = Solicitud::create(['ID_Solicitante' => $solicitante->ID_Usuarios, 'ID_Version_Tramite' => $version->ID_Version_Tramite, 'Estado_Solicitud' => 'Aprobado']);

    $folio = Folio::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'Codigo_Folio' => 'SETC-2026-99999',
        'Fecha_Emision_Folio' => now(),
    ]);

    Resolucion::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Supervisor' => $solicitante->ID_Usuarios,
        'Tipo_Resolucion' => 'Favorable',
        'Fecha_Emision_Resolucion' => now(),
    ]);

    $response = $this->actingAs($solicitante)->get(route('expedientes.resolucion.pdf', $folio->Codigo_Folio));

    $response->assertOk();
    Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) use ($solicitud) {
        return $pdf->viewName === 'pdf.resolucion' &&
               $pdf->viewData['solicitud']->ID_Solicitud === $solicitud->ID_Solicitud;
    });
});
