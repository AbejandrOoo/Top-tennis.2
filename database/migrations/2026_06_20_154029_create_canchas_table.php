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
            $table->string('nombre')->unique();        // Ej: "Cancha Central"
            $table->string('tipo_superficie');          // Arcilla, Sintética, Hierba, Dura
            $table->enum('estado_mantenimiento', ['operativa', 'en_mantenimiento'])
                  ->default('operativa');
            $table->softDeletes();                      // Borrado lógico (integridad)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('canchas');
    }
};
