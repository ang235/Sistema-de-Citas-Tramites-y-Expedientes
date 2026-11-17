<?php

use App\Services\CitaService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('citas:cancelar-vencidas', function (CitaService $citaService) {
    $total = $citaService->cancelarCitasVencidas();
    $this->info("Se cancelaron automáticamente {$total} citas vencidas.");
})->purpose('Cancela automáticamente las citas en estado Programada cuya fecha/hora ya ha expirado.');

Schedule::call(function (CitaService $citaService) {
    $citaService->cancelarCitasVencidas();
})->everyFiveMinutes()->name('citas:cancelar-vencidas');
