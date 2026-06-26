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
        // Amplía el enum de canchas.estado para incluir 'Bloqueada',
        // alineando la BD con lo que ya validan StoreCanchaRequest y UpdateCanchaRequest.
        Schema::table('canchas', function (Blueprint $table) {
            $table->enum('estado', ['Disponible', 'No Disponible', 'Bloqueada'])
                  ->default('Disponible')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('canchas', function (Blueprint $table) {
            $table->enum('estado', ['Disponible', 'No Disponible'])
                  ->default('Disponible')
                  ->change();
        });
    }
};
