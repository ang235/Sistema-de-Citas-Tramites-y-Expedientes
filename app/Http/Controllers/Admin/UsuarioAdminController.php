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

        // KPIs reales
        $kpis = [
            'total_usuarios' => Usuario::count(),
            'revisores' => Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Revisor%'))->count(),
            'supervisores' => Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Supervisor%'))->count(),
            'administradores' => Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Admin%'))->count(),
            'ciudadanos' => Usuario::whereHas('roles', fn ($q) => $q->where('Nombre', 'like', '%Ciudadano%')->orWhere('Nombre', 'like', '%Solicitante%'))->count(),
            'suspendidos' => Usuario::where('Activo', false)->count(),
        ];

        return view('portal.usuarios', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'kpis' => $kpis,
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
                'Activo' => true,
                'Fecha_Creacion' => now(),
            ]);

            $usuario->roles()->sync([$request->validated('id_rol')]);

            $rol = Rol::find($request->validated('id_rol'));
            Bitacora::create([
                'ID_Usuarios' => $idAdmin,
                'Accion' => 'Alta de usuario institucional',
                'Descripcion' => "Usuario {$usuario->Nombre} (#{$usuario->ID_Usuarios}) registrado con rol único: {$rol?->Nombre}.",
                'Fecha_Creacion' => now(),
            ]);
        });

        return back()->with('success', 'Usuario institucional creado y rol asignado exitosamente.');
    }

    public function updateRoles(Request $request, int $id): RedirectResponse
    {
        // Validar estrictamente un único rol
        $validated = $request->validate([
            'id_rol' => ['required', 'integer', 'exists:roles,ID_Rol'],
        ], [
            'id_rol.required' => 'Debe seleccionar un único rol institucional para el usuario.',
            'id_rol.exists' => 'El rol institucional seleccionado no es válido.',
        ]);

        /** @var Usuario $usuario */
        $usuario = Usuario::findOrFail($id);
        $usuario->roles()->sync([$validated['id_rol']]);

        $rol = Rol::find($validated['id_rol']);
        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Actualización de rol de usuario',
            'Descripcion' => "Rol actualizado para usuario #{$id} a: {$rol?->Nombre}.",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', "Rol actualizado exitosamente a {$rol?->Nombre}.");
    }

    public function destroy(int $id): RedirectResponse
    {
        abort_if(Auth::id() === $id, 400, 'No puede deshabilitar su propia cuenta activa de administrador.');

        /** @var Usuario $usuario */
        $usuario = Usuario::findOrFail($id);
        $nuevoEstado = ! ($usuario->Activo ?? true);
        $usuario->Activo = $nuevoEstado;
        $usuario->save();

        $accion = $nuevoEstado ? 'Habilitación de usuario' : 'Deshabilitación de usuario';
        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => $accion,
            'Descripcion' => 'Cuenta '.($nuevoEstado ? 'habilitada' : 'deshabilitada').": {$usuario->Nombre} (#{$id}).",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', "La cuenta de {$usuario->Nombre} ha sido ".($nuevoEstado ? 'habilitada' : 'deshabilitada').' exitosamente.');
    }
}
