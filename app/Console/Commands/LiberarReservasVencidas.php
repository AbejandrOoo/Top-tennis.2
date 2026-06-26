<?php

namespace App\Console\Commands;

use App\Models\Reserva;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

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
