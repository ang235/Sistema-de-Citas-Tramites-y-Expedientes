<?php

declare(strict_types=1);

use App\Mail\PasswordResetMail;
use App\Models\RecuperacionContrasena;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('shows forgot password form', function () {
    $response = $this->get(route('password.request'));

    $response->assertStatus(200);
    $response->assertSee('Recuperar Contraseña');
});

test('sends password reset link to registered user and logs mail', function () {
    Mail::fake();

    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Prueba',
        'Email' => 'ciudadano_test@example.com',
        'Contrasena' => Hash::make('password123'),
        'Activo' => true,
        'Fecha_Creacion' => now(),
    ]);

    $response = $this->post(route('password.email'), [
        'email' => 'ciudadano_test@example.com',
    ]);

    $response->assertSessionHas('status');

    Mail::assertSent(PasswordResetMail::class, function ($mail) use ($usuario) {
        return $mail->hasTo($usuario->Email);
    });

    $this->assertDatabaseHas('recuperacion_contrasena', [
        'ID_Usuarios' => $usuario->ID_Usuarios,
    ]);
});

test('fails when email does not exist for password reset', function () {
    Mail::fake();

    $response = $this->post(route('password.email'), [
        'email' => 'no_existe@example.com',
    ]);

    $response->assertSessionHasErrors(['email']);
    Mail::assertNothingSent();
});

test('can reset password with a valid token', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Reset',
        'Email' => 'reset_user@example.com',
        'Contrasena' => Hash::make('oldpassword123'),
        'Activo' => true,
        'Fecha_Creacion' => now(),
    ]);

    $token = 'test-token-valid-1234567890';

    RecuperacionContrasena::create([
        'ID_Usuarios' => $usuario->ID_Usuarios,
        'Token' => $token,
        'Expiracion' => now()->addMinutes(60),
    ]);

    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => $usuario->Email,
        'password' => 'newpassword456',
        'password_confirmation' => 'newpassword456',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('success');

    $usuario->refresh();
    expect(Hash::check('newpassword456', $usuario->Contrasena))->toBeTrue();

    // Verify token was deleted after consumption
    $this->assertDatabaseMissing('recuperacion_contrasena', [
        'ID_Usuarios' => $usuario->ID_Usuarios,
        'Token' => $token,
    ]);
});

test('cannot reset password with expired token', function () {
    $usuario = Usuario::create([
        'Nombre' => 'Ciudadano Expired',
        'Email' => 'expired_user@example.com',
        'Contrasena' => Hash::make('oldpassword123'),
        'Activo' => true,
        'Fecha_Creacion' => now(),
    ]);

    $token = 'expired-token-12345';

    RecuperacionContrasena::create([
        'ID_Usuarios' => $usuario->ID_Usuarios,
        'Token' => $token,
        'Expiracion' => now()->subMinutes(10),
    ]);

    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => $usuario->Email,
        'password' => 'newpassword456',
        'password_confirmation' => 'newpassword456',
    ]);

    $response->assertSessionHasErrors(['token']);

    $usuario->refresh();
    expect(Hash::check('oldpassword123', $usuario->Contrasena))->toBeTrue();
});
