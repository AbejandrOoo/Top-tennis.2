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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horario_id')->constrained('horarios')->onDelete('restrict');
            $table->foreignId('cobrado_por')->constrained('users')->onDelete('restrict');
            $table->decimal('monto', 8, 2);
            $table->enum('metodo_pago', ['Efectivo', 'Tarjeta', 'Transferencia', 'Otro']);
            $table->enum('estado', ['Pendiente', 'Pagado', 'Reembolsado'])->default('Pagado');
            $table->date('fecha_pago');
            $table->text('notas')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
