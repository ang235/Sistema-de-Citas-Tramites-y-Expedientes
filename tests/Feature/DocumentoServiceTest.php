<?php

declare(strict_types=1);

use App\Models\Bitacora;
use App\Models\Documento;
use App\Models\Requisito;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionTramite;
use App\Services\DocumentoService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('subirNuevaVersion almacena archivo sin sobreescribir anteriores y genera registro en versiones_doc', function () {
    Storage::fake('local');

    $usuario = Usuario::create([
        'Nombre' => 'Solicitante Doc',
        'Email' => 'solicitante_doc@setc.gob',
        'Contrasena' => 'pass123',
    ]);

    $tipo = TipoTramite::create(['Nombre' => 'Tramite A']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $requisito = Requisito::create([
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Nombre' => 'DNI o Pasaporte',
        'Prioridad' => 'Obligatorio',
    ]);

    $solicitud = Solicitud::create([
        'ID_Solicitante' => $usuario->ID_Usuarios,
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
    ]);

    $documento = Documento::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Requisitos' => $requisito->ID_Requisitos,
        'Estado_Doc' => 'Pendiente',
    ]);

    $service = new DocumentoService;

    // 1. Cargar primera versión del archivo
    $archivo1 = UploadedFile::fake()->create('dni_v1.pdf', 300, 'application/pdf');
    $v1 = $service->subirNuevaVersion($documento, $archivo1, $usuario);

    // 2. Cargar segunda versión (subsanación - regla de inmutabilidad / no sobreescritura)
    $archivo2 = UploadedFile::fake()->create('dni_v2.pdf', 350, 'application/pdf');
    $v2 = $service->subirNuevaVersion($documento, $archivo2, $usuario);

    expect($documento->versionesDoc()->count())->toBe(2);
    expect($v1->ID_Version_Doc)->not->toBe($v2->ID_Version_Doc);
    expect($documento->fresh()->Estado_Doc)->toBe('En Revisión');

    // Valida auditoría en bitácora por cada versión
    expect(Bitacora::where('Accion', 'Actualización de Documento')->count())->toBe(2);
});

test('subirNuevaVersion bloquea intentos de carga por usuarios no autorizados', function () {
    Storage::fake('local');

    $dueno = Usuario::create(['Nombre' => 'Dueño', 'Email' => 'dueno@setc.gob', 'Contrasena' => '123']);
    $ajeno = Usuario::create(['Nombre' => 'Ajeno', 'Email' => 'ajeno@setc.gob', 'Contrasena' => '123']);

    $tipo = TipoTramite::create(['Nombre' => 'Tramite B']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $requisito = Requisito::create(['ID_Version_Tramite' => $version->ID_Version_Tramite, 'Nombre' => 'Ficha RUC']);

    $solicitud = Solicitud::create([
        'ID_Solicitante' => $dueno->ID_Usuarios,
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
    ]);

    $documento = Documento::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Requisitos' => $requisito->ID_Requisitos,
    ]);

    $service = new DocumentoService;
    $archivo = UploadedFile::fake()->create('ruc.pdf', 200, 'application/pdf');

    expect(fn () => $service->subirNuevaVersion($documento, $archivo, $ajeno))
        ->toThrow(AuthorizationException::class);
});

test('validarRequisitosCompletos detecta si faltan documentos obligatorios', function () {
    Storage::fake('local');

    $usuario = Usuario::create(['Nombre' => 'Ana', 'Email' => 'ana@setc.gob', 'Contrasena' => '123']);
    $tipo = TipoTramite::create(['Nombre' => 'Tramite C']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);

    $reqObligatorio = Requisito::create([
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Nombre' => 'Requisito Obligatorio',
        'Prioridad' => 'Obligatorio',
    ]);

    $solicitud = Solicitud::create([
        'ID_Solicitante' => $usuario->ID_Usuarios,
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
    ]);

    $doc = Documento::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Requisitos' => $reqObligatorio->ID_Requisitos,
    ]);

    $service = new DocumentoService;

    // Sin versiones cargadas debe dar false
    expect($service->validarRequisitosCompletos($solicitud))->toBeFalse();

    // Tras cargar versión debe dar true
    $archivo = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');
    $service->subirNuevaVersion($doc, $archivo, $usuario);

    expect($service->validarRequisitosCompletos($solicitud))->toBeTrue();
});
