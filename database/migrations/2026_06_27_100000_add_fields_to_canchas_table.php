<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('canchas', function (Blueprint $table) {
            $table->enum('modalidad', ['Singles', 'Dobles', 'Ambos'])
                  ->default('Ambos')
                  ->after('tipo_superficie');

            $table->boolean('iluminacion')
                  ->default(true)
                  ->after('modalidad');

            $table->text('motivo_mantenimiento')
                  ->nullable()
                  ->after('estado_mantenimiento');

            $table->dateTime('fin_mantenimiento')
                  ->nullable()
                  ->after('motivo_mantenimiento');
        });
    }

    public function down(): void
    {
        Schema::table('canchas', function (Blueprint $table) {
            $table->dropColumn(['modalidad', 'iluminacion', 'motivo_mantenimiento', 'fin_mantenimiento']);
        });
    }
};
