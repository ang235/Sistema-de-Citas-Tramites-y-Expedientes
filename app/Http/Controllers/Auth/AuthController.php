<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Bitacora;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = [
            'Email' => $request->validated('email'),
            'password' => $request->validated('password'),
        ];

        /** @var Usuario|null $usuario */
        $usuario = Usuario::where('Email', $request->validated('email'))->first();

        if ($usuario !== null && Hash::check($request->validated('password'), $usuario->Contrasena)) {
            Auth::login($usuario, $request->boolean('remember'));
            $request->session()->regenerate();

            Bitacora::create([
                'ID_Usuarios' => $usuario->ID_Usuarios,
                'Accion' => 'Inicio de sesión',
                'Descripcion' => 'Inicio de sesión exitoso desde IP: ' . $request->ip(),
                'Fecha_Creacion' => now(),
            ]);

            // Redirección inteligente según rol
            if ($usuario->roles()->whereIn('Nombre', ['Administrador', 'Supervisor', 'Revisor', 'Revisor L1', 'Revisor L2'])->exists()) {
                return redirect()->intended(route('portal.dashboard'));
            }

            return redirect()->intended(route('expedientes.index'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $usuario = Usuario::create([
            'Nombre' => $request->validated('nombre'),
            'Email' => $request->validated('email'),
            'Contrasena' => Hash::make($request->validated('password')),
            'Fecha_Creacion' => now(),
        ]);

        // Asignar rol Ciudadano por defecto si existe
        $rolCiudadano = Rol::where('Nombre', 'Ciudadano')->first();
        if ($rolCiudadano !== null) {
            $usuario->roles()->attach($rolCiudadano->ID_Rol);
        }

        Auth::login($usuario);

        Bitacora::create([
            'ID_Usuarios' => $usuario->ID_Usuarios,
            'Accion' => 'Registro de ciudadano',
            'Descripcion' => 'Alta de nueva cuenta ciudadana.',
            'Fecha_Creacion' => now(),
        ]);

        return redirect()->route('expedientes.index')->with('success', 'Cuenta creada con éxito.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $idUsuario = Auth::id();

        if ($idUsuario !== null) {
            Bitacora::create([
                'ID_Usuarios' => $idUsuario,
                'Accion' => 'Cierre de sesión',
                'Descripcion' => 'Sesión cerrada por el usuario.',
                'Fecha_Creacion' => now(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Has cerrado sesión.');
    }
}

