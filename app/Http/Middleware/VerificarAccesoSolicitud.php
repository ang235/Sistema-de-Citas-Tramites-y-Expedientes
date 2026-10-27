<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\AsignacionRevisor;
use App\Models\Solicitud;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarAccesoSolicitud
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No autenticado.',
                ], 401);
            }

            return redirect()->guest(route('login', [], false));
        }

        // Obtener el parámetro del expediente desde la ruta
        $param = $request->route('solicitud')
            ?? $request->route('id')
            ?? $request->route('ID_Solicitud');

        if ($param === null) {
            return $next($request);
        }

        /** @var Solicitud|null $solicitud */
        $solicitud = $param instanceof Solicitud ? $param : Solicitud::find($param);

        if ($solicitud === null) {
            abort(404, 'El expediente o solicitud solicitada no fue encontrada.');
        }

        // 1. El usuario es el solicitante / titular del expediente
        if ((int) $solicitud->ID_Solicitante === (int) $user->ID_Usuarios) {
            return $next($request);
        }

        // 2. Usuarios con rol de Administrador o Supervisor tienen acceso global
        if (! $user->relationLoaded('roles')) {
            $user->load('roles');
        }

        $rolesUsuario = $user->roles->pluck('Nombre')->map(fn ($r) => strtolower((string) $r))->all();

        if (in_array('administrador', $rolesUsuario, true) || in_array('supervisor', $rolesUsuario, true)) {
            return $next($request);
        }

        // 3. Revisores asignados formalmente al expediente
        $estaAsignado = AsignacionRevisor::query()
            ->where('ID_Solicitud', $solicitud->ID_Solicitud)
            ->where('ID_Revisor', $user->ID_Usuarios)
            ->exists();

        if ($estaAsignado) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'No tiene autorización para visualizar o gestionar este expediente.',
            ], 403);
        }

        abort(403, 'Acceso denegado: este expediente pertenece a otro ciudadano o no le ha sido asignado.');
    }
}
