<?php

namespace App\Console\Commands;

use App\Http\Middleware\RedirigirLocalhostAIp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetcIpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setc:ip {ip? : Dirección IP manual a configurar} {--port=8080 : Puerto de la aplicación} {--clear : Elimina la IP configurada para volver a localhost}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detecta, configura o muestra la IP de red local para acceso multidispositivo (celulares y PCs en el mismo Wi-Fi)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = storage_path('app/host_ip.txt');

        if ($this->option('clear')) {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $this->info('✓ Se ha eliminado la IP configurada. El sistema utilizará la configuración predeterminada.');

            return self::SUCCESS;
        }

        $ip = $this->argument('ip');

        if ($ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP)) {
                $this->error("La dirección IP proporcionada [{$ip}] no es válida.");

                return self::FAILURE;
            }
        } else {
            $ip = RedirigirLocalhostAIp::obtenerIpRedActiva() ?: $this->detectarIpSistema();
        }

        if (! $ip) {
            $this->warn('No se pudo detectar automáticamente la IP de la red. Por favor proporcione una: php artisan setc:ip <tu-ip>');

            return self::FAILURE;
        }

        // Guardar la IP en storage/app/host_ip.txt
        File::ensureDirectoryExists(dirname($filePath));
        File::put($filePath, $ip);

        $port = $this->option('port') ?: (parse_url((string) config('app.url'), PHP_URL_PORT) ?: '8080');
        $urlAcceso = "http://{$ip}:{$port}";

        $this->newLine();
        $this->line('<bg=blue;fg=white;options=bold> ====================================================================== </>');
        $this->line('<bg=blue;fg=white;options=bold>        SETC — SISTEMA DE EXPEDIENTES DE TRÁMITES Y CITAS               </>');
        $this->line('<bg=blue;fg=white;options=bold> ====================================================================== </>');
        $this->newLine();
        $this->info(" ✓ IP de Red Activa:   {$ip}");
        $this->info(" ✓ URL de Acceso:      {$urlAcceso}");
        $this->newLine();
        $this->comment(' Acceso desde teléfonos móviles / tablets en la misma red Wi-Fi:');
        $this->line('   1. Conecta tu celular a la misma red Wi-Fi.');
        $this->line("   2. Abre Chrome o Safari e ingresa a: <href={$urlAcceso}>{$urlAcceso}</>");
        $this->line('   3. Los códigos QR generados funcionarán automáticamente con esta dirección IP.');
        $this->newLine();
        $this->line('<fg=gray> Para cambiar de red en el futuro, ejecuta: php artisan setc:ip <nueva-ip></>');
        $this->line('<fg=gray> O ejecuta abrir-setc.bat desde la raíz del proyecto para auto-detectarla.</>');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Intenta detectar la IP principal del sistema operativo mediante comandos nativos.
     */
    protected function detectarIpSistema(): ?string
    {
        // En Windows (mediante PowerShell)
        if (str_starts_with(PHP_OS, 'WIN')) {
            try {
                $cmd = 'powershell -NoProfile -Command "$r = Get-NetRoute -DestinationPrefix \'0.0.0.0/0\' -ErrorAction SilentlyContinue | Sort-Object RouteMetric | Select-Object -First 1; if ($r) { (Get-NetIPAddress -InterfaceIndex $r.InterfaceIndex -AddressFamily IPv4 -ErrorAction SilentlyContinue | Select-Object -First 1).IPAddress }"';
                $output = @shell_exec($cmd);
                $ip = trim((string) $output);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            } catch (\Throwable) {
                // Continuar a fallback
            }
        }

        // En Linux / Docker
        try {
            $output = @shell_exec("ip route get 1.1.1.1 2>/dev/null | awk '{print $7}'");
            $ip = trim((string) $output);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        } catch (\Throwable) {
            // Continuar a fallback
        }

        return null;
    }
}
