<?php

namespace Database\Factories;

use App\Models\Tarifa;
use Illuminate\Database\Eloquent\Factories\Factory;

class TarifaFactory extends Factory
{
    protected $model = Tarifa::class;

    public function definition(): array
    {
        $turno = $this->faker->randomElement(['Mañana', 'Tarde', 'Noche', 'Fin de semana', 'Hora punta']);

        return [
            'nombre_tarifa' => 'Tarifa ' . $turno,
            'precio'        => $this->faker->randomElement([12.00, 15.00, 18.00, 20.00, 22.00, 25.00]),
        ];
    }
}
