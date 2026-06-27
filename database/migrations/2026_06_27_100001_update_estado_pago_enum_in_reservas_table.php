<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Agrega 'cancelado_por_mantenimiento' al ENUM de estado_pago
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE reservas MODIFY COLUMN estado_pago
             ENUM('pendiente','aprobado','anulada','cancelado_por_mantenimiento')
             NOT NULL DEFAULT 'pendiente'"
        );
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement(
            "ALTER TABLE reservas MODIFY COLUMN estado_pago
             ENUM('pendiente','aprobado','anulada')
             NOT NULL DEFAULT 'pendiente'"
        );
    }
};
