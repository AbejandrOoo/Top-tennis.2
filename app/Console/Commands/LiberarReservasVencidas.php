<?php

namespace App\Console\Commands;

use App\Models\Reserva;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

// COMANDO ARTISAN: SE EJECUTA CADA MINUTO VIA SCHEDULER (routes/console.php,
// everyMinute + withoutOverlapping). LA LOGICA REAL VIVE EN Reserva::liberarVencidas()
// PARA NO DUPLICARLA CON EL MODO "LAZY" QUE CORRE AL CARGAR LAS VISTAS DE RESERVAS.
// PROBAR A MANO CON: php artisan reservas:liberar-vencidas
#[Signature('reservas:liberar-vencidas')]
#[Description('Anula las reservas en Efectivo vencidas y libera sus horarios (no-show).')]
class LiberarReservasVencidas extends Command
{
    public function handle(): int
    {
        $liberadas = Reserva::liberarVencidas();

        $this->info("Reservas vencidas liberadas: {$liberadas}");

        return self::SUCCESS;
    }
}
