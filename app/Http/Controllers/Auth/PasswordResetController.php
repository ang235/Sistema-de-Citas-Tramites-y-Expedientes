<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\RecuperacionContrasena;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        ]);

        /** @var Usuario $usuario */
        $usuario = Usuario::where('Email', $validated['email'])->firstOrFail();

        $token = Str::random(64);

        RecuperacionContrasena::create([
            'ID_Usuarios' => $usuario->ID_Usuarios,
            'Token' => $token,
            'Expiracion' => now()->addMinutes(60),
        ]);

        Bitacora::create([
            'ID_Usuarios' => $usuario->ID_Usuarios,
            'Accion' => 'Solicitud de recuperación de contraseña',
            'Descripcion' => 'Generación de token temporal de recuperación.',
            'Fecha_Creacion' => now(),
        ]);

        // En un entorno de producción se enviaría por correo; aquí confirmamos el envío simulado
        return back()->with('status', 'Hemos generado la solicitud de restablecimiento para tu correo.');
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
        ]);

        /** @var Usuario $usuario */
        $usuario = Usuario::where('Email', $validated['email'])->firstOrFail();

        $record = RecuperacionContrasena::where('ID_Usuarios', $usuario->ID_Usuarios)
            ->where('Token', $validated['token'])
            ->where('Expiracion', '>', now())
            ->latest('created_at')
            ->first();

        if ($record === null) {
            return back()->withErrors(['token' => 'El token de recuperación es inválido o ha expirado.']);
        }

        $usuario->update([
            'Contrasena' => Hash::make($validated['password']),
        ]);

        $record->delete();

        Bitacora::create([
            'ID_Usuarios' => $usuario->ID_Usuarios,
            'Accion' => 'Restablecimiento de contraseña',
            'Descripcion' => 'Contraseña actualizada mediante token de recuperación.',
            'Fecha_Creacion' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Tu contraseña ha sido actualizada exitosamente.');
    }
}

