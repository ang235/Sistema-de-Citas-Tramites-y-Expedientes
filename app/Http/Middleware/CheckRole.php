<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
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

        if (empty($roles)) {
            return $next($request);
        }

        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allowedRoles[] = strtolower($trimmed);
                }
            }
        }

        // Cargar roles del usuario autenticado si no están cargados
        if (! $user->relationLoaded('roles')) {
            $user->load('roles');
        }

        // Alias internos para simplificar el uso en rutas
        // 'ciudadano' => cualquier rol que NO sea admin/revisor/supervisor
        // 'revisor'   => rol que contiene 'Revisor'
        // 'supervisor' => rol que contiene 'Supervisor'
        // 'admin'     => rol que contiene 'Admin'
        /** @var Usuario $user */
        $matchAlias = function (string $alias) use ($user): bool {
            return match ($alias) {
                'ciudadano', 'solicitante' => $user->esCiudadano(),
                'revisor' => $user->esRevisor(),
                'supervisor' => $user->esSupervisor(),
                'admin', 'administrador' => $user->esAdministrador(),
                default => $user->roles->contains(
                    fn ($r) => strcasecmp((string) $r->Nombre, $alias) === 0
                ),
            };
        };

        $hasRole = false;
        foreach ($allowedRoles as $allowed) {
            if ($matchAlias($allowed)) {
                $hasRole = true;
                break;
            }
        }

        if (! $hasRole) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Acceso denegado: rol insuficiente.',
                    'roles_requeridos' => $allowedRoles,
                ], 403);
            }

            // Rol institucional intentando acceder a área ciudadana → redirigir a su panel
            if ($user instanceof Usuario) {
                if ($user->esRevisor()) {
                    return redirect()->route('portal.revisor-dashboard')
                        ->with('warning', 'Como Revisor Técnico no tienes acceso a las secciones ciudadanas.');
                }
                if ($user->esSupervisor()) {
                    return redirect()->route('portal.supervisor-dashboard')
                        ->with('warning', 'Como Supervisor no tienes acceso a las secciones ciudadanas.');
                }
                if ($user->esAdministrador()) {
                    return redirect()->route('portal.dashboard')
                        ->with('warning', 'Como Administrador no tienes acceso a las secciones ciudadanas.');
                }
            }

            abort(403, 'No cuenta con los permisos requeridos para acceder a esta sección.');
        }

        return $next($request);
    }
}
