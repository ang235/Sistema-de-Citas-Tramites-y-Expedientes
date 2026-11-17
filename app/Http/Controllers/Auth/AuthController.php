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
            if (! ($usuario->Activo ?? true)) {
                return back()->withErrors([
                    'email' => 'Esta cuenta institucional se encuentra deshabilitada temporalmente. Contacte a la administración.',
                ])->onlyInput('email');
            }

            Auth::login($usuario, $request->boolean('remember'));
            $request->session()->regenerate();

            // Asegurar carga fresca de roles asignados
            $usuario->load('roles');

            // Redirección inteligente según rol institucional
            if ($usuario->esAdministrador()) {
                return redirect()->route('portal.dashboard');
            }

            if ($usuario->esSupervisor()) {
                return redirect()->route('portal.supervisor-dashboard');
            }

            if ($usuario->esRevisor()) {
                return redirect()->route('portal.revisor-dashboard');
            }

            return redirect()->route('expedientes.index');
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
        $rolCiudadano = Rol::where('Nombre', 'like', '%Ciudadano%')->first()
            ?? Rol::where('Nombre', 'like', '%Solicitante%')->first()
            ?? Rol::where('Nombre', 'Ciudadano')->first();
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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Has cerrado sesión.');
    }
}
