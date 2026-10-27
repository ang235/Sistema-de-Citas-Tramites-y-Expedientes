<?php

declare(strict_types=1);

use App\Models\AsignacionRevisor;
use App\Models\Bitacora;
use App\Models\Documento;
use App\Models\Requisito;
use App\Models\Revision;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionDoc;
use App\Models\VersionTramite;
use App\Services\RevisionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('flujo de revision tecnica registra observaciones, subsanaciones y auditoria en bitacora', function () {
    $ciudadano = Usuario::create(['Nombre' => 'Ciudadano', 'Email' => 'c@setc.gob', 'Contrasena' => '123']);
    $revisor = Usuario::create(['Nombre' => 'Revisor L1', 'Email' => 'rev@setc.gob', 'Contrasena' => '123']);
    $supervisor = Usuario::create(['Nombre' => 'Supervisor', 'Email' => 'sup@setc.gob', 'Contrasena' => '123']);

    $tipo = TipoTramite::create(['Nombre' => 'Tramite Inspección']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $solicitud = Solicitud::create(['ID_Solicitante' => $ciudadano->ID_Usuarios, 'ID_Version_Tramite' => $version->ID_Version_Tramite]);

    $asignacion = AsignacionRevisor::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Revisor' => $revisor->ID_Usuarios,
        'ID_Asignador' => $supervisor->ID_Usuarios,
        'Fecha_Asignacion' => now(),
    ]);

    $service = new RevisionService;

    // 1. Iniciar revisión técnica
    $revision = $service->iniciarRevision($asignacion, $revisor);
    expect($revision->Resultado)->toBe('En Revisión');

    // 2. Registrar observación técnica
    $observacion = $service->registrarObservacion($revision, 'El plano arquitectónico carece de firma pericial.', $revisor);
    expect($observacion->Estado_Obs)->toBe('Abierta');

    // 3. Subsanar observación por el ciudadano
    $documento = Documento::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Requisitos' => Requisito::create(['ID_Version_Tramite' => $version->ID_Version_Tramite, 'Nombre' => 'Plano'])->ID_Requisitos,
    ]);
    $versionDoc = VersionDoc::create([
        'ID_Documento' => $documento->ID_Documento,
        'Fecha_Carga' => now(),
    ]);

    $respuesta = $service->subsanarObservacion($observacion, 'Se adjunta plano debidamente firmado.', $versionDoc, $ciudadano);
    expect($observacion->fresh()->Estado_Obs)->toBe('Subsanada');
    expect($respuesta->Mensaje_Respuesta)->toContain('plano');

    // 4. Dictaminar la revisión (Favorable)
    $revisionDictaminada = $service->dictaminarRevision($revision, 'Favorable', $revisor);
    expect($revisionDictaminada->Resultado)->toBe('Favorable');

    // Validar auditoría completa en Bitácora
    expect(Bitacora::where('Accion', 'Inicio de Revisión Técnica')->exists())->toBeTrue();
    expect(Bitacora::where('Accion', 'Apertura de Observación')->exists())->toBeTrue();
    expect(Bitacora::where('Accion', 'Subsanación de Observación')->exists())->toBeTrue();
    expect(Bitacora::where('Accion', 'Cambio de Estado en Revisión')->exists())->toBeTrue();
});

test('dictaminarRevision rechaza resultados invalidos', function () {
    $revisor = Usuario::create(['Nombre' => 'Revisor', 'Email' => 'r@setc.gob', 'Contrasena' => '123']);
    $tipo = TipoTramite::create(['Nombre' => 'Tipo Test']);
    $version = VersionTramite::create(['ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite, 'No_Version' => 'v1']);
    $solicitud = Solicitud::create(['ID_Solicitante' => $revisor->ID_Usuarios, 'ID_Version_Tramite' => $version->ID_Version_Tramite]);

    $asignacion = AsignacionRevisor::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Revisor' => $revisor->ID_Usuarios,
        'ID_Asignador' => $revisor->ID_Usuarios,
        'Fecha_Asignacion' => now(),
    ]);
    $revision = Revision::create([
        'ID_Asignacion' => $asignacion->ID_Asignacion,
        'Resultado' => 'En Revisión',
        'Fecha_Revision' => now(),
    ]);

    $service = new RevisionService;

    expect(fn () => $service->dictaminarRevision($revision, 'ResultadoInexistente', $revisor))
        ->toThrow(DomainException::class);
});
