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
        Schema::table('pagos', function (Blueprint $table) {
            // N° de operación que el cliente coloca al pagar por Yape/Plin (opcional)
            $table->string('numero_operacion', 50)->nullable()->after('metodo_pago');
            // Código único del ticket digital, ej. TT-2406
            $table->string('codigo_validacion', 20)->nullable()->unique()->after('numero_operacion');
            // Yape y Plin son intercambiables: un solo método. Efectivo se paga en recepción.
            $table->enum('metodo_pago', ['Yape/Plin', 'Efectivo'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropUnique(['codigo_validacion']);
            $table->dropColumn(['numero_operacion', 'codigo_validacion']);
            $table->enum('metodo_pago', ['Efectivo', 'Tarjeta', 'Transferencia', 'Otro'])->change();
        });
    }
};
