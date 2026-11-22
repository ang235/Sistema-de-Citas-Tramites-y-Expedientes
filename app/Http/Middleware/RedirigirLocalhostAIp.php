<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class RedirigirLocalhostAIp
{
    /**
     * Maneja la redirección cuando el acceso se realiza por localhost o 127.0.0.1,
     * redirigiendo al usuario a la IP activa de la red para garantizar que los enlaces,
     * códigos QR y accesos multidispositivo funcionen correctamente.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // No intervenir en ambiente de pruebas ni en el healthcheck de Docker
        if (app()->environment('testing') || $request->is('up') || ! $request->isMethod('GET')) {
            return $next($request);
        }

        $host = $request->getHost();

        // Solo actuar cuando se accede mediante localhost o 127.0.0.1
        if ($host === 'localhost' || $host === '127.0.0.1') {
            $targetIp = self::obtenerIpRedActiva();

            if ($targetIp && $targetIp !== 'localhost' && $targetIp !== '127.0.0.1') {
                $scheme = $request->getScheme();
                $port = $request->getPort();
                $portSuffix = ($port && ! in_array($port, [80, 443], true)) ? ":{$port}" : '';
                $targetUrl = "{$scheme}://{$targetIp}{$portSuffix}".$request->getRequestUri();

                return redirect()->to($targetUrl, 302);
            }
        }

        return $next($request);
    }

    /**
     * Obtiene la IP local activa desde storage/app/host_ip.txt o desde config('app.url').
     */
    public static function obtenerIpRedActiva(): ?string
    {
        $filePath = storage_path('app/host_ip.txt');
        if (File::exists($filePath)) {
            $ip = trim((string) File::get($filePath));
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        $configuredUrl = config('app.url');
        $configuredHost = parse_url((string) $configuredUrl, PHP_URL_HOST);

        if ($configuredHost && filter_var($configuredHost, FILTER_VALIDATE_IP)) {
            return $configuredHost;
        }

        return null;
    }
}
