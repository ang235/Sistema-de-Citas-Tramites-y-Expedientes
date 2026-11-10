<?php

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('revisor logs in and is redirected to revisor dashboard', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Revisor Test',
        'Email' => 'revisor_test@test.com',
        'Contrasena' => Hash::make('password123'),
        'Activo' => true,
        'Fecha_Creacion' => now(),
    ]);

    $rol = Rol::create([
        'Nombre' => 'Revisor Técnico',
        'Descripcion' => 'Rol revisor',
    ]);

    $usuario->roles()->attach($rol->ID_Rol);

    $response = $this->post('/login', [
        'email' => 'revisor_test@test.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('portal.revisor-dashboard'));
});

test('supervisor logs in and is redirected to supervisor dashboard', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Supervisor Test',
        'Email' => 'supervisor_test@test.com',
        'Contrasena' => Hash::make('password123'),
        'Activo' => true,
        'Fecha_Creacion' => now(),
    ]);

    $rol = Rol::create([
        'Nombre' => 'Supervisor / Dictaminador',
        'Descripcion' => 'Rol supervisor',
    ]);

    $usuario->roles()->attach($rol->ID_Rol);

    $response = $this->post('/login', [
        'email' => 'supervisor_test@test.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('portal.supervisor-dashboard'));
});

test('ciudadano logs in and is redirected to expedientes index', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Test',
        'Email' => 'ciudadano_test@test.com',
        'Contrasena' => Hash::make('password123'),
        'Activo' => true,
        'Fecha_Creacion' => now(),
    ]);

    $rol = Rol::create([
        'Nombre' => 'Solicitante / Ciudadano',
        'Descripcion' => 'Rol ciudadano',
    ]);

    $usuario->roles()->attach($rol->ID_Rol);

    $response = $this->post('/login', [
        'email' => 'ciudadano_test@test.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('expedientes.index'));
});
