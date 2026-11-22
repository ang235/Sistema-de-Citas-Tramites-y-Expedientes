<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Asegurar columna Activo en usuarios sin borrar datos
        try {
            if (Schema::hasTable('usuarios') && ! Schema::hasColumn('usuarios', 'Activo')) {
                Schema::table('usuarios', function (Blueprint $table) {
                    $table->boolean('Activo')->default(true);
                });
            }

            if (Schema::hasTable('asignacion_revisor') && ! Schema::hasColumn('asignacion_revisor', 'Estado_Asignacion')) {
                Schema::table('asignacion_revisor', function (Blueprint $table) {
                    $table->string('Estado_Asignacion', 50)->default('Activo');
                });
            }
        } catch (\Throwable) {
            // Ignorar si ya está aplicada en concurrencia
        }

        Gate::define('manage-users', fn ($user) => $user->esAdministrador());
        Gate::define('mesa-dictamen', fn ($user) => $user->esRevisor());
        Gate::define('supervisar', fn ($user) => $user->esSupervisor());

        // Enlazar dinámicamente URLs a la IP/Host por el que se accede
        if (! $this->app->runningInConsole() && request()->getHttpHost()) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        }
    }
}
