<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('horario_id')->constrained()->onDelete('restrict');
            // Capa 1 anti-doble-reserva: un horario solo admite una reserva
            $table->unique('horario_id');

            $table->enum('metodo_pago', ['Yape', 'Efectivo']);
            // Anti-reuso de comprobante: el mismo N° de operación no paga 2 reservas
            $table->string('numero_operacion', 50)->nullable()->unique();

            // pendiente (Efectivo sin pagar) | aprobado (Yape o caja) | anulada (no-show/caducada)
            $table->enum('estado_pago', ['pendiente', 'aprobado', 'anulada'])->default('pendiente');

            $table->string('codigo_validacion', 20)->unique(); // Ticket #TT-xxxx
            // Solo Efectivo: límite para pagar presencialmente = hora_inicio - 30 min
            $table->dateTime('expira_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Query eficiente para la auto-liberación de vencidas
            $table->index(['estado_pago', 'expira_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
