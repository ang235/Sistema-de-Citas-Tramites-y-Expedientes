<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Bitacora;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RegistrarBitacora
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $accion = null): Response
    {
        $response = $next($request);

        $this->registrar($request, $response, $accion);

        return $response;
    }

    /**
     * Registra la acción del usuario en la bitácora institucional.
     */
    protected function registrar(Request $request, Response $response, ?string $accion): void
    {
        $user = $request->user();

        // La tabla bitacora requiere ID_Usuarios
        if ($user === null) {
            return;
        }

        // Si no se especifica acción personalizada, registrar métodos mutantes (POST, PUT, PATCH, DELETE)
        if ($accion === null && ! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }

        try {
            $accionFinal = $accion ?? sprintf('%s %s', $request->method(), $request->path());

            // Limpieza de parámetros sensibles antes de serializar
            $payload = $request->except([
                'password',
                'Contrasena',
                'password_confirmation',
                'token',
                'Token',
                '_token',
            ]);

            $detalles = [
                'ruta' => $request->path(),
                'metodo' => $request->method(),
                'ip' => $request->ip(),
                'status_http' => $response->getStatusCode(),
                'payload' => $payload,
            ];

            Bitacora::create([
                'ID_Usuarios' => $user->ID_Usuarios,
                'Accion' => substr($accionFinal, 0, 150),
                'Descripcion' => json_encode($detalles, JSON_UNESCAPED_UNICODE),
                'Fecha_Creacion' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error registrando evento en Bitácora: '.$e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}
