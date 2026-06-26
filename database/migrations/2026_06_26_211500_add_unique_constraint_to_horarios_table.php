<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            // Evita doble reserva: misma cancha, misma fecha, mismo inicio
            $table->unique(['cancha_id', 'fecha', 'hora_inicio'], 'horarios_cancha_fecha_hora_unique');
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropUnique('horarios_cancha_fecha_hora_unique');
        });
    }
};
