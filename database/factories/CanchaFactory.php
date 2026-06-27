<?php

namespace Database\Factories;

use App\Models\Cancha;
use Illuminate\Database\Eloquent\Factories\Factory;

class CanchaFactory extends Factory
{
    protected $model = Cancha::class;

    public function definition(): array
    {
        $superficie = $this->faker->randomElement(['Arcilla', 'Sintética', 'Hierba', 'Dura']);

        return [
            'nombre'               => 'Cancha ' . $this->faker->bothify('??-###'),
            'tipo_superficie'      => $superficie,
            'imagen'               => Cancha::IMAGENES[$superficie],
            'estado_mantenimiento' => $this->faker->randomElement(['operativa', 'operativa', 'operativa', 'en_mantenimiento']),
        ];
    }

    public function operativa(): static
    {
        return $this->state(['estado_mantenimiento' => 'operativa']);
    }

    public function enMantenimiento(): static
    {
        return $this->state(['estado_mantenimiento' => 'en_mantenimiento']);
    }
}
