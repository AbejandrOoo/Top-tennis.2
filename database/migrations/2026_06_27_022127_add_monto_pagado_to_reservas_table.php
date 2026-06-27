<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            // Snapshot del precio cobrado al momento de la reserva.
            // Garantiza inmutabilidad histórica aunque la Tarifa cambie después.
            $table->decimal('monto_pagado', 8, 2)->after('estado_pago')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropColumn('monto_pagado');
        });
    }
};
