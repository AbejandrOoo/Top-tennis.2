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
        Schema::table('canchas', function (Blueprint $table) {
            $table->enum('modalidad', ['Singles', 'Dobles'])->default('Singles')->after('tipo');
            $table->unsignedTinyInteger('capacidad')->default(2)->after('modalidad');
        });
    }

    public function down(): void
    {
        Schema::table('canchas', function (Blueprint $table) {
            $table->dropColumn(['modalidad', 'capacidad']);
        });
    }
};
