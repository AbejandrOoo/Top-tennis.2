<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            // MySQL usa el UNIQUE como índice del FK, así que hay que:
            // 1) soltar el FK, 2) soltar el UNIQUE, 3) recrear el FK
            // (MySQL generará automáticamente un índice ordinario para el nuevo FK).
            // El UNIQUE bloqueaba las re-reservas: tras cancelar (soft-delete) o
            // auto-liberar (estado=anulada), el registro permanecía en la tabla y
            // el INSERT de la re-reserva fallaba con constraint violation.
            // La capa anti-race ya está cubierta por el UPDATE atómico WHERE estado='disponible'.
            $table->dropForeign('reservas_horario_id_foreign');
            $table->dropIndex('reservas_horario_id_unique');
            $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign('reservas_horario_id_foreign');
            $table->unique('horario_id');
            $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('restrict');
        });
    }
};
