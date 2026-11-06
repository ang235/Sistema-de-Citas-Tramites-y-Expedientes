<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTipoTramiteRequest;
use App\Models\Bitacora;
use App\Models\TipoTramite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TipoTramiteController extends Controller
{
    public function index(): View
    {
        $tramites = TipoTramite::withCount('versiones')
            ->orderBy('Nombre')
            ->paginate(15);

        return view('portal.parametros', [
            'tramites' => $tramites,
            'tiposTramite' => $tramites,
        ]);
    }

    public function store(StoreTipoTramiteRequest $request): RedirectResponse
    {
        $tramite = TipoTramite::create([
            'Nombre' => $request->validated('nombre'),
            'Descripcion' => $request->validated('descripcion'),
            'Estado_Tipo_Tr' => $request->validated('estado_tipo_tr'),
        ]);

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Creación de tipo de trámite',
            'Descripcion' => "Nuevo trámite registrado: {$tramite->Nombre} (#{$tramite->ID_Tipo_Tramite}).",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', 'Trámite normativo creado con éxito.');
    }

    public function update(StoreTipoTramiteRequest $request, int $id): RedirectResponse
    {
        /** @var TipoTramite $tramite */
        $tramite = TipoTramite::findOrFail($id);

        $tramite->update([
            'Nombre' => $request->validated('nombre'),
            'Descripcion' => $request->validated('descripcion'),
            'Estado_Tipo_Tr' => $request->validated('estado_tipo_tr'),
        ]);

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Actualización de trámite',
            'Descripcion' => "Trámite #{$id} actualizado en catálogo.",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', 'Trámite actualizado.');
    }

    public function destroy(int $id): RedirectResponse
    {
        /** @var TipoTramite $tramite */
        $tramite = TipoTramite::findOrFail($id);
        $nombre = $tramite->Nombre;
        $tramite->delete();

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Eliminación de trámite',
            'Descripcion' => "Trámite eliminado: {$nombre} (#{$id}).",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', 'Trámite eliminado del catálogo.');
    }
}

