<?php

declare(strict_types=1);

use App\Models\Requisito;
use App\Models\TipoTramite;
use App\Models\VersionTramite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('el endpoint del asistente requiere un mensaje válido', function () {
    $response = $this->postJson(route('asistente.chat'), []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['mensaje']);
});

test('el asistente responde cordialmente a un saludo con sugerencias', function () {
    $response = $this->postJson(route('asistente.chat'), [
        'mensaje' => 'Hola, buenos días',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ])
        ->assertJsonStructure([
            'success',
            'respuesta',
            'sugerencias',
        ]);

    expect($response->json('respuesta'))->toContain('¡Hola!');
    expect($response->json('sugerencias'))->toBeArray()->not->toBeEmpty();
});

test('el asistente explica las reglas de citas cuando se le pregunta por citas o turnos', function () {
    $response = $this->postJson(route('asistente.chat'), [
        'mensaje' => '¿Cómo puedo agendar una cita?',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $texto = $response->json('respuesta');
    expect($texto)->toContain('agendar tu cita');
    expect($texto)->toContain('15 días hábiles');
    expect($texto)->toContain('1 cita activa a la vez');
});

test('el asistente devuelve los requisitos de un trámite consultado', function () {
    $tramite = TipoTramite::create([
        'Nombre' => 'Licencia de Funcionamiento Test',
        'Descripcion' => 'Trámite municipal de apertura de establecimiento comercial.',
        'Estado_Tipo_Tr' => 'Activo',
    ]);

    $version = VersionTramite::create([
        'ID_Tipo_Tramite' => $tramite->ID_Tipo_Tramite,
        'No_Version' => '1.0',
        'Estado_Ver_Tr' => 'Vigente',
        'Fecha_Inicio_Vig' => now()->toDateString(),
    ]);

    Requisito::create([
        'ID_Version_Tramite' => $version->ID_Version_Tramite,
        'Nombre' => 'Croquis de Ubicación y Planos de Distribución',
        'Prioridad' => 'Obligatorio',
        'Formato' => 'PDF',
    ]);

    $response = $this->postJson(route('asistente.chat'), [
        'mensaje' => '¿Qué requisitos piden para la Licencia de Funcionamiento Test?',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $texto = $response->json('respuesta');
    expect($texto)->toContain('Licencia de Funcionamiento Test');
    expect($texto)->toContain('Croquis de Ubicación');
    expect($texto)->toContain('PDF');
});

test('el asistente rechaza consultas que no tienen que ver con el sistema', function () {
    $response = $this->postJson(route('asistente.chat'), [
        'mensaje' => '¿Cómo preparar una pizza de pepperoni italiana?',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $texto = $response->json('respuesta');
    expect($texto)->toContain('Lo sentimos, el sistema solo puede ayudarte');
    expect($texto)->toContain('SETC');
});

test('el asistente pasa al modelo local cuando la cuota de gemini se agota con 429', function () {
    config(['services.gemini.key' => 'fake-gemini-key']);

    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'error' => [
                'code' => 429,
                'message' => 'Resource has been exhausted (e.g. check quota).',
                'status' => 'RESOURCE_EXHAUSTED',
            ],
        ], 429),
    ]);

    $response = $this->postJson(route('asistente.chat'), [
        'mensaje' => '¿Cómo puedo agendar una cita?',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $texto = $response->json('respuesta');
    expect($texto)->toContain('agendar tu cita');
    expect(Cache::has('gemini_cuota_agotada'))->toBeTrue();
});
