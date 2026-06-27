<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('canchas', function (Blueprint $table) {
            $table->dateTime('inicio_mantenimiento')
                  ->nullable()
                  ->after('motivo_mantenimiento');
        });
    }

    public function down(): void
    {
        Schema::table('canchas', function (Blueprint $table) {
            $table->dropColumn('inicio_mantenimiento');
        });
    }
};
