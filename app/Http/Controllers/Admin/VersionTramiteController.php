<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVersionTramiteRequest;
use App\Models\Bitacora;
use App\Models\VersionTramite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VersionTramiteController extends Controller
{
    public function store(StoreVersionTramiteRequest $request): RedirectResponse
    {
        $version = DB::transaction(function () use ($request): VersionTramite {
            if ($request->validated('estado_ver_tr') === 'Vigente') {
                VersionTramite::where('ID_Tipo_Tramite', $request->validated('id_tipo_tramite'))
                    ->where('Estado_Ver_Tr', 'Vigente')
                    ->update(['Estado_Ver_Tr' => 'Histórica']);
            }

            return VersionTramite::create([
                'ID_Tipo_Tramite' => $request->validated('id_tipo_tramite'),
                'No_Version' => $request->validated('no_version'),
                'Estado_Ver_Tr' => $request->validated('estado_ver_tr'),
                'Fecha_Inicio_Vig' => $request->validated('fecha_inicio_vig') ?? now()->toDateString(),
            ]);
        });

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Creación de versión de trámite',
            'Descripcion' => "Versión {$version->No_Version} para trámite #{$version->ID_Tipo_Tramite} registrada.",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', 'Versión de trámite registrada exitosamente.');
    }

    public function activarVigencia(int $id): RedirectResponse
    {
        /** @var VersionTramite $version */
        $version = VersionTramite::findOrFail($id);

        DB::transaction(function () use ($version): void {
            VersionTramite::where('ID_Tipo_Tramite', $version->ID_Tipo_Tramite)
                ->where('Estado_Ver_Tr', 'Vigente')
                ->update(['Estado_Ver_Tr' => 'Histórica']);

            $version->update([
                'Estado_Ver_Tr' => 'Vigente',
                'Fecha_Inicio_Vig' => now()->toDateString(),
            ]);

            Bitacora::create([
                'ID_Usuarios' => (int) Auth::id(),
                'Accion' => 'Activación de vigencia de versión',
                'Descripcion' => "Versión #{$version->ID_Version_Tramite} establecida como Vigente.",
                'Fecha_Creacion' => now(),
            ]);
        });

        return back()->with('success', 'La versión seleccionada ha sido marcada como Vigente.');
    }

    public function destroy(int $id): RedirectResponse
    {
        /** @var VersionTramite $version */
        $version = VersionTramite::findOrFail($id);
        $version->delete();

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Eliminación de versión de trámite',
            'Descripcion' => "Versión #{$id} eliminada.",
            'Fecha_Creacion' => now(),
        ]);

        return back()->with('success', 'Versión eliminada.');
    }
}

