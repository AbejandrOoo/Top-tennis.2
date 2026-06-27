<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE canchas SET tipo_superficie = 'Césped Artificial'
             WHERE tipo_superficie = 'Sintética'"
        );
    }

    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE canchas SET tipo_superficie = 'Sintética'
             WHERE tipo_superficie = 'Césped Artificial'"
        );
    }
};
