<?php

declare(strict_types=1);

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ciudadano\StoreCitaRequest;
use App\Models\Bitacora;
use App\Models\Cita;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CitaCiudadanoController extends Controller
{
    public function index(): View
    {
        $idUsuario = (int) Auth::id();

        $citas = Cita::where('ID_Usuarios', $idUsuario)
            ->with(['solicitud.folio', 'solicitud.versionTramite.tipoTramite'])
            ->orderBy('Fecha', 'desc')
            ->orderBy('Hora', 'desc')
            ->paginate(10);

        return view('public.citas.index', [
            'citas' => $citas,
        ]);
    }

    public function create(Request $request): View
    {
        $idSolicitud = $request->query('id_solicitud');
        $solicitudes = Solicitud::where('ID_Solicitante', Auth::id())
            ->with(['folio', 'versionTramite.tipoTramite'])
            ->get();

        return view('public.citas.create', [
            'idSolicitudSeleccionada' => $idSolicitud,
            'solicitudes' => $solicitudes,
        ]);
    }

    public function store(StoreCitaRequest $request): RedirectResponse
    {
        $idUsuario = (int) Auth::id();

        // Validar si ya existe cita para ese día y hora en la sede o usuario
        $existe = Cita::where('Fecha', $request->validated('fecha'))
            ->where('Hora', $request->validated('hora'))
            ->where('Estado_Citas', '!=', 'Cancelada')
            ->exists();

        if ($existe) {
            return back()->withErrors(['hora' => 'El horario seleccionado ya se encuentra ocupado.'])->withInput();
        }

        $cita = Cita::create([
            'ID_Usuarios' => $idUsuario,
            'ID_Solicitud' => $request->validated('id_solicitud'),
            'Fecha' => $request->validated('fecha'),
            'Hora' => $request->validated('hora'),
            'Estado_Citas' => 'Programada',
        ]);

        Bitacora::create([
            'ID_Usuarios' => $idUsuario,
            'Accion' => 'Agendación de cita',
            'Descripcion' => "Cita agendada para {$cita->Fecha->format('Y-m-d')} a las {$cita->Hora}.",
            'Fecha_Creacion' => now(),
        ]);

        return redirect()->route('citas.pase', ['idCita' => $cita->ID_Citas])
            ->with('success', 'Tu cita ha sido confirmada.');
    }

    public function paseDigital(int $idCita): View
    {
        /** @var Cita $cita */
        $cita = Cita::with(['usuario', 'solicitud.folio', 'solicitud.versionTramite.tipoTramite'])
            ->where('ID_Usuarios', Auth::id())
            ->findOrFail($idCita);

        return view('public.citas.pase', [
            'cita' => $cita,
        ]);
    }

    public function cancelar(int $idCita): RedirectResponse
    {
        /** @var Cita $cita */
        $cita = Cita::where('ID_Usuarios', Auth::id())->findOrFail($idCita);

        $cita->update(['Estado_Citas' => 'Cancelada']);

        Bitacora::create([
            'ID_Usuarios' => (int) Auth::id(),
            'Accion' => 'Cancelación de cita',
            'Descripcion' => "Cita #{$cita->ID_Citas} cancelada por el solicitante.",
            'Fecha_Creacion' => now(),
        ]);

        return redirect()->route('citas.index')->with('info', 'La cita fue cancelada correctamente.');
    }
}

