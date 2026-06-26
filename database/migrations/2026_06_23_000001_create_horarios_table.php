<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            // El admin crea horarios (slots) asignando cancha y tarifa
            $table->foreignId('cancha_id')->constrained('canchas')->onDelete('restrict');
            $table->foreignId('tarifa_id')->constrained('tarifas')->onDelete('restrict');
            $table->dateTime('hora_inicio');
            $table->dateTime('hora_fin');
            $table->enum('estado', ['disponible', 'reservado'])->default('disponible');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
