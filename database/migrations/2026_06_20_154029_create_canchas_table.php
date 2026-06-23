<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('canchas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Ej: "Cancha Central"
            $table->string('tipo');             // Arcilla o Sintética
            $table->enum('estado', ['Disponible', 'No Disponible'])->default('Disponible');
            $table->softDeletes();              // Borrado lógico para auditoría
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canchas');
    }
};