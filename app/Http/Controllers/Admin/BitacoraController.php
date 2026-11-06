<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitacoraController extends Controller
{
    public function index(Request $request): View
    {
        $idUsuario = $request->query('id_usuario');
        $accion = $request->query('accion');
        $fecha = $request->query('fecha');

        $bitacoras = Bitacora::query()
            ->when($idUsuario, function ($query, $uid): void {
                $query->where('ID_Usuarios', $uid);
            })
            ->when($accion, function ($query, $acc): void {
                $query->where('Accion', 'like', "%{$acc}%");
            })
            ->when($fecha, function ($query, $f): void {
                $query->whereDate('Fecha_Creacion', $f);
            })
            ->with('usuario')
            ->orderBy('ID_Bitacora', 'desc')
            ->paginate(25);

        $usuarios = Usuario::select('ID_Usuarios', 'Nombre', 'Email')->get();

        return view('portal.auditoria', [
            'bitacoras' => $bitacoras,
            'usuarios' => $usuarios,
            'idUsuarioFiltro' => $idUsuario,
            'accionFiltro' => $accion,
            'fechaFiltro' => $fecha,
        ]);
    }
}

