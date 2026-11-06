<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRequisitoRequest;
use App\Models\Bitacora;
use App\Models\Requisito;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RequisitoController extends Controller
{
    public function store(StoreRequisitoRequest $request): RedirectResponse
    {
        $requisito = Requisito::create([
            'ID_Version_Tramite' => $request->validated('id_version_tramite'),
            'Nombre' => $request->validated('nombre'),
            'Prioridad' => $request->validated('prioridad'),
            'Formato' => $request->validated('formato'),
        ]);

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Alta de requisito normativo',
            'Descripcion' => "Requisito {$requisito->Nombre} añadido a la versión #{$requisito->ID_Version_Tramite}.",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', 'Requisito añadido a la versión normativa.');
    }

    public function destroy(int $id): RedirectResponse
    {
        /** @var Requisito $requisito */
        $requisito = Requisito::findOrFail($id);
        $nombre = $requisito->Nombre;
        $requisito->delete();

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Baja de requisito normativo',
            'Descripcion' => "Requisito {$nombre} (#{$id}) eliminado.",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', 'Requisito eliminado.');
    }
}

