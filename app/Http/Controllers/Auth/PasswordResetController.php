<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\Bitacora;
use App\Models\RecuperacionContrasena;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:usuarios,Email'],
        ], [
            'email.exists' => 'No encontramos ninguna cuenta asociada a ese correo electrónico.',
        ]);

        /** @var Usuario $usuario */
        $usuario = Usuario::where('Email', $validated['email'])->firstOrFail();

        // Eliminar tokens anteriores del mismo usuario para evitar acumulación
        RecuperacionContrasena::where('ID_Usuarios', $usuario->ID_Usuarios)->delete();

        $token = Str::random(64);

        RecuperacionContrasena::create([
            'ID_Usuarios' => $usuario->ID_Usuarios,
            'Token' => $token,
            'Expiracion' => now()->addMinutes(60),
        ]);

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $usuario->Email,
        ]);

        // Envío real del correo
        Mail::to($usuario->Email)->send(new PasswordResetMail($usuario, $resetUrl));

        Bitacora::create([
            'ID_Usuarios' => $usuario->ID_Usuarios,
            'Accion' => 'Solicitud de recuperación de contraseña',
            'Descripcion' => 'Correo de restablecimiento enviado a '.$usuario->Email,
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('status', '¡Listo! Te hemos enviado un correo con el enlace para restablecer tu contraseña. Revisa también tu carpeta de spam.');
    }

    public function showResetPasswordForm(string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:usuarios,Email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        /** @var Usuario $usuario */
        $usuario = Usuario::where('Email', $validated['email'])->firstOrFail();

        $record = RecuperacionContrasena::where('ID_Usuarios', $usuario->ID_Usuarios)
            ->where('Token', $validated['token'])
            ->where('Expiracion', '>', now())
            ->latest('created_at')
            ->first();

        if ($record === null) {
            return back()->withErrors(['token' => 'El enlace de recuperación es inválido o ha expirado. Solicita uno nuevo.']);
        }

        $usuario->update([
            'Contrasena' => Hash::make($validated['password']),
        ]);

        // Eliminar todos los tokens usados del usuario
        RecuperacionContrasena::where('ID_Usuarios', $usuario->ID_Usuarios)->delete();

        Bitacora::create([
            'ID_Usuarios' => $usuario->ID_Usuarios,
            'Accion' => 'Restablecimiento de contraseña',
            'Descripcion' => 'Contraseña actualizada mediante enlace de recuperación.',
            'Fecha_Creacion' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Tu contraseña ha sido actualizada exitosamente. Ya puedes iniciar sesión.');
    }
}
