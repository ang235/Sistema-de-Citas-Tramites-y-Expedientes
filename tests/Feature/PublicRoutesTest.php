<?php

declare(strict_types=1);

use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Models\VersionTramite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('public home page loads successfully', function () {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('Ventanilla Única Digital');
});

test('tramites catalog loads successfully', function () {
    $response = $this->get(route('tramites.index'));

    $response->assertStatus(200);
    $response->assertSee('Catálogo de Trámites y Servicios');
});

test('tramite show page loads with existing tramite', function () {
    $tipo = TipoTramite::create([
        'Nombre' => 'Constancia de Zonificación',
        'Descripcion' => 'Trámite de uso de suelo',
        'Estado_Tipo_Tr' => 'Activo',
    ]);

    $version = VersionTramite::create([
        'ID_Tipo_Tramite' => $tipo->ID_Tipo_Tramite,
        'No_Version' => '1.0',
        'Estado_Ver_Tr' => 'Vigente',
        'Fecha_Inicio_Vig' => now()->subDay(),
    ]);

    $response = $this->get(route('tramites.show', $tipo->ID_Tipo_Tramite));

    $response->assertStatus(200);
    $response->assertSee('Constancia de Zonificación');
});

test('ayuda center loads successfully', function () {
    $response = $this->get(route('ayuda.index'));

    $response->assertStatus(200);
    $response->assertSee('Mesa de Ayuda y Soporte');
});

test('login page loads successfully', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
    $response->assertSee('Acceso Ciudadano e Institucional');
});

test('register page loads successfully', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
    $response->assertSee('Crear Cuenta Ciudadana');
});

test('unauthenticated users are redirected from profile', function () {
    $response = $this->get(route('profile.show'));

    $response->assertRedirect(route('login'));
});

test('unauthenticated users are redirected from expedientes', function () {
    $response = $this->get(route('expedientes.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated citizen can view profile and update name', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Original',
        'Email' => 'original@example.com',
        'Contrasena' => Hash::make('password123'),
        'Activo' => true,
        'Fecha_Creacion' => now(),
    ]);

    $response = $this->actingAs($usuario)->get(route('profile.show'));
    $response->assertStatus(200);
    $response->assertSee('Ciudadano Original');

    $updateResponse = $this->actingAs($usuario)->put(route('profile.update'), [
        'nombre' => 'Ciudadano Modificado',
        'email' => 'modificado@example.com',
    ]);

    $updateResponse->assertSessionHas('success');

    $usuario->refresh();
    expect($usuario->Nombre)->toBe('Ciudadano Modificado');
    expect($usuario->Email)->toBe('modificado@example.com');
});
