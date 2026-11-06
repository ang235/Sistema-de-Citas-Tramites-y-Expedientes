<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsuarioRequest;
use App\Models\Bitacora;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioAdminController extends Controller
{
    public function index(Request $request): View
    {
        $rolFiltro = $request->query('rol');
        $search = $request->query('buscar');

        $usuarios = Usuario::query()
            ->when($rolFiltro, function ($query, $rol): void {
                $query->whereHas('roles', function ($q) use ($rol): void {
                    $q->where('Nombre', $rol);
                });
            })
            ->when($search, function ($query, $term): void {
                $query->where(function ($q) use ($term): void {
                    $q->where('Nombre', 'like', "%{$term}%")
                        ->orWhere('Email', 'like', "%{$term}%");
                });
            })
            ->with('roles')
            ->orderBy('ID_Usuarios', 'desc')
            ->paginate(15);

        $roles = Rol::all();

        return view('portal.usuarios', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'rolFiltro' => $rolFiltro,
            'busqueda' => $search,
        ]);
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $idAdmin = (int) Auth::id();

        DB::transaction(function () use ($request, $idAdmin): void {
            $usuario = Usuario::create([
                'Nombre' => $request->validated('nombre'),
                'Email' => $request->validated('email'),
                'Contrasena' => Hash::make($request->validated('password')),
                'Fecha_Creacion' => now(),
            ]);

            $usuario->roles()->sync($request->validated('roles'));

            Bitacora::create([
                'ID_Usuarios' => $idAdmin,
                'Accion' => 'Alta de usuario administrativo',
                'Descripcion' => "Usuario {$usuario->Nombre} (#{$usuario->ID_Usuarios}) registrado con roles.",
                'Fecha_Creacion' => now(),
            ]);
        });

        return back()->with('success', 'Usuario creado y roles asignados.');
    }

    public function updateRoles(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['integer', 'exists:roles,ID_Rol'],
        ]);

        /** @var Usuario $usuario */
        $usuario = Usuario::findOrFail($id);
        $usuario->roles()->sync($validated['roles']);

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Actualización de roles de usuario',
            'Descripcion' => "Roles actualizados para usuario #{$id}.",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', 'Roles actualizados con éxito.');
    }

    public function destroy(int $id): RedirectResponse
    {
        abort_if(Auth::id() === $id, 400, 'No puede eliminar su propia cuenta activa.');

        /** @var Usuario $usuario */
        $usuario = Usuario::findOrFail($id);
        $nombre = $usuario->Nombre;
        $usuario->delete();

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Eliminación de usuario',
            'Descripcion' => "Cuenta eliminada: {$nombre} (#{$id}).",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', 'Usuario eliminado del sistema.');
    }
}

