<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-liberación de no-shows en Efectivo: cada minuto anula las vencidas
// y libera el horario. (Modo lazy en ReservaController como red de seguridad.)
Schedule::command('reservas:liberar-vencidas')->everyMinute()->withoutOverlapping();
