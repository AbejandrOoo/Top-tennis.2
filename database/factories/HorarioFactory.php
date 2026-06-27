<?php

namespace Database\Factories;

use App\Models\Cancha;
use App\Models\Horario;
use App\Models\Tarifa;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class HorarioFactory extends Factory
{
    protected $model = Horario::class;

    public function definition(): array
    {
        // Hora de inicio aleatoria a futuro (1-14 días) en bloques de hora completa
        $inicio = Carbon::now()
            ->addDays($this->faker->numberBetween(1, 14))
            ->setTime($this->faker->numberBetween(7, 20), 0, 0);

        return [
            'cancha_id'   => Cancha::factory()->operativa(),
            'tarifa_id'   => Tarifa::factory(),
            'hora_inicio' => $inicio,
            'hora_fin'    => $inicio->copy()->addHour(),
            'estado'      => 'disponible',
        ];
    }

    public function reservado(): static
    {
        return $this->state(['estado' => 'reservado']);
    }
}
