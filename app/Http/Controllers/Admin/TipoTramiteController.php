<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTipoTramiteRequest;
use App\Models\Bitacora;
use App\Models\TipoTramite;
use App\Models\VersionTramite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TipoTramiteController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'activos');
        $buscar = $request->query('buscar');

        $query = TipoTramite::withCount('versiones')
            ->with(['versionesTramite.requisitos'])
            ->when($tab === 'inactivos', function ($q): void {
                $q->where('Estado_Tipo_Tr', 'Inactivo');
            }, function ($q): void {
                $q->where('Estado_Tipo_Tr', '!=', 'Inactivo');
            })
            ->when($buscar, function ($q, $term): void {
                $q->where('Nombre', 'like', "%{$term}%")
                    ->orWhere('Descripcion', 'like', "%{$term}%");
            })
            ->orderBy('Nombre');

        $tiposTramite = $query->paginate(15)->appends($request->query());

        $totalActivos = TipoTramite::where('Estado_Tipo_Tr', '!=', 'Inactivo')->count();
        $totalInactivos = TipoTramite::where('Estado_Tipo_Tr', 'Inactivo')->count();

        $kpis = [
            'total_tramites' => TipoTramite::count(),
            'versiones_activas' => VersionTramite::where('Vigente', true)->count(),
            'total_activos' => $totalActivos,
            'total_inactivos' => $totalInactivos,
            'pendientes_revision' => TipoTramite::where('Estado_Tipo_Tr', '!=', 'Activo')->where('Estado_Tipo_Tr', '!=', 'Inactivo')->count(),
        ];

        return view('portal.parametros', [
            'tramites' => $tiposTramite,
            'tiposTramite' => $tiposTramite,
            'tab' => $tab,
            'buscar' => $buscar,
            'totalActivos' => $totalActivos,
            'totalInactivos' => $totalInactivos,
            'kpis' => $kpis,
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

        $nuevoEstado = $tramite->Estado_Tipo_Tr === 'Inactivo' ? 'Activo' : 'Inactivo';
        $tramite->update(['Estado_Tipo_Tr' => $nuevoEstado]);

        $accionLabel = $nuevoEstado === 'Inactivo' ? 'deshabilitado' : 'reactivado';

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => $nuevoEstado === 'Inactivo' ? 'Deshabilitación de trámite' : 'Reactivación de trámite',
            'Descripcion' => "Trámite {$accionLabel}: {$nombre} (#{$id}).",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', "Trámite {$nombre} marcado como {$nuevoEstado} exitosamente.");
    }
}
