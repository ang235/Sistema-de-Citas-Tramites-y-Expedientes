<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('public.home', [
            'seccion' => 'profile',
            'usuario' => $usuario,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('usuarios', 'Email')->ignore($usuario->ID_Usuarios, 'ID_Usuarios'),
            ],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $dataToUpdate = [
            'Nombre' => $validated['nombre'],
            'Email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            if (empty($validated['current_password']) || ! Hash::check($validated['current_password'], $usuario->Contrasena)) {
                return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
            }
            $dataToUpdate['Contrasena'] = Hash::make($validated['password']);
        }

        $usuario->update($dataToUpdate);

        Bitacora::create([
            'ID_Usuarios' => $usuario->ID_Usuarios,
            'Accion' => 'Actualización de perfil',
            'Descripcion' => 'El usuario actualizó sus datos de perfil.',
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', 'Perfil actualizado con éxito.');
    }
}

