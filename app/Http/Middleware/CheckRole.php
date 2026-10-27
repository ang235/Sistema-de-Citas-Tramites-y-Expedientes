<?php

declare(strict_types=1);

namespace App\Http\Middleware;

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
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        // Cargar roles del usuario autenticado si no están cargados
        if (! $user->relationLoaded('roles')) {
            $user->load('roles');
        }

        $userRoles = $user->roles->pluck('Nombre')->all();

        $hasRole = false;
        foreach ($allowedRoles as $allowed) {
            foreach ($userRoles as $userRole) {
                if (strcasecmp($allowed, (string) $userRole) === 0) {
                    $hasRole = true;
                    break 2;
                }
            }
        }

        if (! $hasRole) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Acceso denegado: rol insuficiente.',
                    'roles_requeridos' => $allowedRoles,
                ], 403);
            }

            abort(403, 'No cuenta con los permisos requeridos para acceder a esta sección.');
        }

        return $next($request);
    }
}
