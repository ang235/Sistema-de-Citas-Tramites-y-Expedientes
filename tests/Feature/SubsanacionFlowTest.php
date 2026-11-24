<?php

declare(strict_types=1);

use App\Models\AsignacionRevisor;
use App\Models\Documento;
use App\Models\Folio;
use App\Models\Observacion;
use App\Models\Requisito;
use App\Models\Revision;
use App\Models\Rol;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionDoc;
use App\Models\VersionTramite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('flujo completo de subsanacion: timeline retrocede a fase 3 y revisor puede reevaluar y emitir nuevo dictamen sin alterar el expediente original', function () {
    Storage::fake('local');

    // 1. Crear usuarios y roles
    $ciudadano = Usuario::create([
        'Nombre' => 'Carlos Ciudadano',
        'Email' => 'carlos@test.gob',
        'Contrasena' => bcrypt('password123'),
        'Activo' => true,
    ]);
    $rolCiudadano = Rol::firstOrCreate(['Nombre' => 'Ciudadano']);
    $ciudadano->roles()->attach($rolCiudadano->ID_Rol);

    $revisor = Usuario::create([
        'Nombre' => 'Ing. Revisor Técnico',
        'Email' => 'revisor@test.gob',
        'Contrasena' => bcrypt('password123'),
        'Activo' => true,
    ]);
    $rolRevisor = Rol::firstOrCreate(['Nombre' => 'Revisor']);
    $revisor->roles()->attach($rolRevisor->ID_Rol);

    $supervisor = Usuario::create([
        'Nombre' => 'Lic. Supervisor',
        'Email' => 'supervisor@test.gob',
        'Contrasena' => bcrypt('password123'),
        'Activo' => true,
    ]);
    $rolSupervisor = Rol::firstOrCreate(['Nombre' => 'Supervisor']);
    $supervisor->roles()->attach($rolSupervisor->ID_Rol);

    // 2. Expediente inicial con 1 documento
    $tipo = TipoTramite::create(['Nombre' => 'Licencia de Funcionamiento']);
    $versionTramite = VersionTramite::create([
        'ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite,
        'No_Version' => 'v1.0',
    ]);
    $requisito = Requisito::create([
        'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
        'Nombre' => 'Memoria Descriptiva',
        'Prioridad' => 'Obligatorio',
        'Formato' => 'PDF',
    ]);

    $solicitud = Solicitud::create([
        'ID_Solicitante' => $ciudadano->ID_Usuarios,
        'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
        'Estado_Solicitud' => 'En_Revision',
    ]);

    $folio = Folio::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'Codigo_Folio' => 'SETC-2026-TEST1',
        'Fecha_Emision' => now(),
    ]);

    $doc = Documento::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Requisitos' => $requisito->ID_Requisitos,
        'Estado_Doc' => 'Pendiente',
    ]);

    $rutaV1 = 'expedientes/'.$solicitud->ID_Solicitud.'/v1_original.pdf';
    Storage::disk('local')->put($rutaV1, '%PDF-1.4 original');
    $version1 = VersionDoc::create([
        'ID_Documento' => $doc->ID_Documento,
        'Ruta_Archivo' => $rutaV1,
        'Nombre_Original' => 'memoria_v1.pdf',
        'Fecha_Carga' => now()->subDays(2),
    ]);

    $asignacion = AsignacionRevisor::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Revisor' => $revisor->ID_Usuarios,
        'ID_Asignador' => $supervisor->ID_Usuarios,
        'Estado_Asignacion' => 'Activo',
        'Fecha_Asignacion' => now()->subDay(),
    ]);

    // 3. Revisor emite primer dictamen: Observado con una observación
    $revision1 = Revision::create([
        'ID_Asignacion' => $asignacion->ID_Asignacion,
        'Resultado' => 'Observado',
        'Fecha_Revision' => now()->subHours(5),
    ]);
    $observacion = Observacion::create([
        'ID_Revision' => $revision1->ID_Revision,
        'Detalles' => 'Falta sello del ingeniero colegiado en la memoria.',
        'Estado_Obs' => 'Abierta',
    ]);
    $doc->update(['Estado_Doc' => 'Observado']);
    $solicitud->update(['Estado_Solicitud' => 'Observado']);

    // 4. Ciudadano solventa la observación cargando un nuevo archivo (Versión 2)
    $archivoSubsanado = UploadedFile::fake()->create('memoria_v2_subsanada.pdf', 300, 'application/pdf');

    $responseSolventar = $this->actingAs($ciudadano)->post(route('expedientes.solventar', $observacion->ID_Observacion), [
        'mensaje_respuesta' => 'Se adjunta memoria con el sello y firma correspondiente.',
        'archivo' => $archivoSubsanado,
    ]);

    $responseSolventar->assertSessionHasNoErrors();
    $responseSolventar->assertRedirect(route('expedientes.show', 'SETC-2026-TEST1'));

    // Validar estado de base de datos tras subsanación
    expect($observacion->fresh()->Estado_Obs)->toBe('Subsanada');
    expect($doc->fresh()->Estado_Doc)->toBe('Subsanado');
    expect($solicitud->fresh()->Estado_Solicitud)->toBe('En_Revision');
    expect($doc->versionesDoc()->count())->toBe(2);

    $version2 = $doc->versionesDoc()->latest('ID_Version_Doc')->first();
    expect($version2->Nombre_Original)->toBe('memoria_v2_subsanada.pdf');

    // 5. Verificar que la timeline en expedientes.show retrocede:
    // Fase 3 está activa (subsanación en revisión), y Fase 4 y 5 NO están marcadas como completadas
    $responseShow = $this->actingAs($ciudadano)->get(route('expedientes.show', 'SETC-2026-TEST1'));
    $responseShow->assertOk();
    $responseShow->assertSee('Subsanación enviada');
    $responseShow->assertSee('Pendiente de nuevo dictamen técnico');
    $responseShow->assertDontSee('Calificación: Observado');

    // 6. El revisor consulta la consola de evaluación
    $responseEval = $this->actingAs($revisor)->get(route('portal.expedientes.evaluar', $solicitud->ID_Solicitud));
    $responseEval->assertOk();
    // Ve el recaudo con estado Subsanado y el historial de versiones
    $responseEval->assertSee('Subsanado');
    $responseEval->assertSee('Historial de Versiones del Documento');
    $responseEval->assertSee('memoria_v2_subsanada.pdf');
    $responseEval->assertSee('Se adjunta memoria con el sello y firma correspondiente.');

    // 7. El revisor puede ver/descargar ambas versiones sin alterar el archivo original
    $respV1 = $this->actingAs($revisor)->get(route('portal.documentos.archivo', [$doc->ID_Documento, 'version_id' => $version1->ID_Version_Doc]));
    $respV1->assertOk();

    $respV2 = $this->actingAs($revisor)->get(route('portal.documentos.archivo', [$doc->ID_Documento, 'version_id' => $version2->ID_Version_Doc]));
    $respV2->assertOk();

    // 8. El revisor califica el documento como Aprobado
    $responseDocEval = $this->actingAs($revisor)->post(route('portal.documentos.evaluar', $doc->ID_Documento), [
        'estado_doc' => 'Aprobado',
    ]);
    $responseDocEval->assertSessionHasNoErrors();
    expect($doc->fresh()->Estado_Doc)->toBe('Aprobado');

    // 9. El revisor emite un nuevo Dictamen Técnico (Favorable / Aprobado)
    $responseRevision2 = $this->actingAs($revisor)->post(route('portal.revisiones.store', $asignacion->ID_Asignacion), [
        'resultado' => 'Aprobado',
        'observaciones' => [],
    ]);
    $responseRevision2->assertSessionHasNoErrors();
    $responseRevision2->assertRedirect(route('portal.mesa-dictamen'));

    // Ambas revisiones existen en el historial (no se borró ni alteró la primera)
    expect($asignacion->revisiones()->count())->toBe(2);
    expect($solicitud->fresh()->Estado_Solicitud)->toBe('Aprobado');

    // 10. Ahora la timeline avanza: Fase 3 y Fase 4 completadas, Fase 5 pendiente de firma
    $responseShowAprobado = $this->actingAs($ciudadano)->get(route('expedientes.show', 'SETC-2026-TEST1'));
    $responseShowAprobado->assertOk();
    $responseShowAprobado->assertSee('Recaudos técnicos validados');
    $responseShowAprobado->assertSee('Calificación: Aprobado');
    $responseShowAprobado->assertSee('Pendiente de firma resolutiva');
});

test('el timeline avanza inmediatamente a Fase 4 completada y Fase 5 activa cuando el revisor dictamina Aprobado sobre un expediente con subsanacion', function () {
    Storage::fake('local');

    $ciudadano = Usuario::create([
        'Nombre' => 'Carlos Trejo',
        'Email' => 'sus@sus.com',
        'Contrasena' => bcrypt('password123'),
        'Activo' => true,
    ]);
    $rolCiudadano = Rol::firstOrCreate(['Nombre' => 'Ciudadano']);
    $ciudadano->roles()->attach($rolCiudadano->ID_Rol);

    $revisor = Usuario::create([
        'Nombre' => 'Jesus Hernandez Bustos',
        'Email' => 'revisor_jesus@test.gob',
        'Contrasena' => bcrypt('password123'),
        'Activo' => true,
    ]);
    $rolRevisor = Rol::firstOrCreate(['Nombre' => 'Revisor']);
    $revisor->roles()->attach($rolRevisor->ID_Rol);

    $supervisor = Usuario::create([
        'Nombre' => 'Supervisor General',
        'Email' => 'super@test.gob',
        'Contrasena' => bcrypt('password123'),
        'Activo' => true,
    ]);
    $rolSupervisor = Rol::firstOrCreate(['Nombre' => 'Supervisor']);
    $supervisor->roles()->attach($rolSupervisor->ID_Rol);

    $tipo = TipoTramite::create(['Nombre' => 'Tramite para Licencia de Manejo']);
    $versionTramite = VersionTramite::create([
        'ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite,
        'No_Version' => 'v1.0',
    ]);
    $req1 = Requisito::create([
        'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
        'Nombre' => 'Identificación Oficial',
        'Prioridad' => 'Obligatorio',
        'Formato' => 'PDF',
    ]);
    $req2 = Requisito::create([
        'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
        'Nombre' => 'Comprobante de Domicilio',
        'Prioridad' => 'Obligatorio',
        'Formato' => 'PDF',
    ]);

    $solicitud = Solicitud::create([
        'ID_Solicitante' => $ciudadano->ID_Usuarios,
        'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
        'Estado_Solicitud' => 'En_Revision',
    ]);

    $folio = Folio::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'Codigo_Folio' => 'SETC-2026-TREJO1',
        'Fecha_Emision' => now(),
    ]);

    // Recaudo 1 aprobado, Recaudo 2 subsanado
    $doc1 = Documento::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Requisitos' => $req1->ID_Requisitos,
        'Estado_Doc' => 'Aprobado',
    ]);
    $doc2 = Documento::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Requisitos' => $req2->ID_Requisitos,
        'Estado_Doc' => 'Subsanado',
    ]);

    $asignacion = AsignacionRevisor::create([
        'ID_Solicitud' => $solicitud->ID_Solicitud,
        'ID_Revisor' => $revisor->ID_Usuarios,
        'ID_Asignador' => $supervisor->ID_Usuarios,
        'Estado_Asignacion' => 'Activo',
        'Fecha_Asignacion' => now(),
    ]);

    // El revisor emite Dictamen Técnico Aprobado directamente
    $response = $this->actingAs($revisor)->post(route('portal.revisiones.store', $asignacion->ID_Asignacion), [
        'resultado' => 'Aprobado',
        'observaciones' => [],
    ]);
    $response->assertSessionHasNoErrors();

    // Verificamos que todos los documentos pasaron a Aprobado
    expect($doc2->fresh()->Estado_Doc)->toBe('Aprobado');
    expect($solicitud->fresh()->Estado_Solicitud)->toBe('Aprobado');

    // Verificamos que la vista del ciudadano refleja el avance completo en el timeline
    $responseShow = $this->actingAs($ciudadano)->get(route('expedientes.show', 'SETC-2026-TREJO1'));
    $responseShow->assertOk();
    $responseShow->assertSee('Recaudos técnicos validados');
    $responseShow->assertSee('Calificación: Aprobado');
    $responseShow->assertSee('Pendiente de firma resolutiva');
    $responseShow->assertDontSee('Subsanación enviada — En revisión técnica');
    $responseShow->assertDontSee('Pendiente de nuevo dictamen técnico');
});
