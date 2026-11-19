<?php

declare(strict_types=1);

use App\Models\Requisito;
use App\Models\TipoTramite;
use App\Models\VersionTramite;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    expect($response->json('respuesta'))->toContain('Bienvenido al **Asistente Virtual del SETC**');
    expect($response->json('sugerencias'))->toBeArray()->not->toBeEmpty();
});

test('el asistente explica las reglas de citas cuando se le pregunta por citas o turnos', function () {
    $response = $this->postJson(route('asistente.chat'), [
        'mensaje' => '¿Cómo puedo agendar una cita?',
    ]);

    $response->assertOk()
        ->assertJson(['success' => true]);

    $texto = $response->json('respuesta');
    expect($texto)->toContain('Reglas para Agendar Cita');
    expect($texto)->toContain('15 Días');
    expect($texto)->toContain('1 cita activa');
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
