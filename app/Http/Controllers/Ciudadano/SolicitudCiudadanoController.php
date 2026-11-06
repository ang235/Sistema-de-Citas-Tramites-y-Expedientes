<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ciudadano\StoreSolicitudRequest;
use App\Models\Bitacora;
use App\Models\DatoSolicitud;
use App\Models\Documento;
use App\Models\Folio;
use App\Models\Solicitud;
use App\Models\TipoTramite;
use App\Models\VersionDoc;
use App\Models\VersionTramite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudCiudadanoController extends Controller
{
    public function create(int $idTipoTramite): View
    {
        /** @var TipoTramite $tramite */
        $tramite = TipoTramite::where('Estado_Tipo_Tr', 'Activo')
            ->with(['versiones' => function ($query): void {
                $query->where('Estado_Ver_Tr', 'Vigente')->with('requisitos');
            }])
            ->findOrFail($idTipoTramite);

        /** @var VersionTramite|null $versionVigente */
        $versionVigente = $tramite->versiones->first();

        abort_if($versionVigente === null, 404, 'El trámite no cuenta con una versión vigente para inicio.');

        return view('public.solicitudes.create', [
            'tramite' => $tramite,
            'versionVigente' => $versionVigente,
        ]);
    }

    public function store(StoreSolicitudRequest $request): RedirectResponse
    {
        $idUsuario = (int) Auth::id();

        /** @var VersionTramite $versionTramite */
        $versionTramite = VersionTramite::with('requisitos')
            ->where('ID_Version_Tramite', $request->validated('id_version_tramite'))
            ->where('Estado_Ver_Tr', 'Vigente')
            ->firstOrFail();

        $solicitud = DB::transaction(function () use ($request, $idUsuario, $versionTramite): Solicitud {
            // 1. Crear la Solicitud
            $solicitud = Solicitud::create([
                'ID_Solicitante' => $idUsuario,
                'ID_Version_Tramite' => $versionTramite->ID_Version_Tramite,
                'Fecha_Creacion_Solicitud' => now(),
            ]);

            // 2. Generar el Folio oficial
            $codigoFolio = sprintf('SETC-%s-%05d', date('Y'), $solicitud->ID_Solicitud);
            Folio::create([
                'ID_Solicitud' => $solicitud->ID_Solicitud,
                'Codigo_Folio' => $codigoFolio,
                'Fecha_Emision' => now(),
            ]);

            // 3. Registrar datos complementarios
            if ($request->filled('descripcion')) {
                DatoSolicitud::create([
                    'ID_Solicitud' => $solicitud->ID_Solicitud,
                    'Descripcion_Sol' => (string) $request->validated('descripcion'),
                ]);
            }

            // 4. Crear los registros de documentos según los requisitos normativos
            $archivos = $request->file('documentos', []);

            foreach ($versionTramite->requisitos as $requisito) {
                $doc = Documento::create([
                    'ID_Solicitud' => $solicitud->ID_Solicitud,
                    'ID_Requisitos' => $requisito->ID_Requisitos,
                    'Estado_Doc' => isset($archivos[$requisito->ID_Requisitos]) ? 'Cargado' : 'Pendiente',
                ]);

                if (isset($archivos[$requisito->ID_Requisitos])) {
                    $file = $archivos[$requisito->ID_Requisitos];
                    $file->store("expedientes/{$solicitud->ID_Solicitud}");

                    VersionDoc::create([
                        'ID_Documento' => $doc->ID_Documento,
                        'Fecha_Carga' => now(),
                    ]);
                }
            }

            // 5. Auditoría en Bitácora
            Bitacora::create([
                'ID_Usuarios' => $idUsuario,
                'Accion' => 'Creación de expediente de solicitud',
                'Descripcion' => "Se ha generado la solicitud con folio {$codigoFolio}.",
                'Fecha_Creacion' => now(),
            ]);

            return $solicitud;
        });

        $solicitud->load('folio');
        $codigoFolio = $solicitud->folio?->Codigo_Folio ?? (string) $solicitud->ID_Solicitud;

        return redirect()->route('expedientes.show', ['codigoFolio' => $codigoFolio])
            ->with('success', "Expediente {$codigoFolio} registrado exitosamente.");
    }
}

