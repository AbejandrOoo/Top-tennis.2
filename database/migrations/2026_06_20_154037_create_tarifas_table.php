<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();
            // Restrict protege las tarifas si intentan borrar físicamente la cancha
            $table->foreignId('cancha_id')->constrained('canchas')->onDelete('restrict');
            $table->decimal('precio_hora', 8, 2);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('turno');
            $table->enum('estado', ['Activa', 'Inactiva'])->default('Activa');
            $table->softDeletes(); // Borrado lógico financiero
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarifas');
    }
};