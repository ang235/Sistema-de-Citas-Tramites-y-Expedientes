<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\TipoTramite;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TramiteCiudadanoController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('buscar');

        $tramites = TipoTramite::query()
            ->where('Estado_Tipo_Tr', 'Activo')
            ->when($search, function ($query, $term): void {
                $query->where('Nombre', 'like', "%{$term}%")
                    ->orWhere('Descripcion', 'like', "%{$term}%");
            })
            ->with(['versiones' => function ($query): void {
                $query->where('Estado_Ver_Tr', 'Vigente')->with('requisitos');
            }])
            ->paginate(12);

        if ($request->routeIs('home')) {
            return view('public.home', [
                'tramites' => $tramites,
                'busqueda' => $search,
            ]);
        }

        return view('public.tramites.index', [
            'tramites' => $tramites,
            'busqueda' => $search,
        ]);
    }

    public function show(int $id): View
    {
        /** @var TipoTramite $tramite */
        $tramite = TipoTramite::where('Estado_Tipo_Tr', 'Activo')
            ->with(['versiones' => function ($query): void {
                $query->where('Estado_Ver_Tr', 'Vigente')->with('requisitos');
            }])
            ->findOrFail($id);

        $versionVigente = $tramite->versiones->first();

        return view('public.tramites.show', [
            'tramite' => $tramite,
            'versionVigente' => $versionVigente,
        ]);
    }
}

