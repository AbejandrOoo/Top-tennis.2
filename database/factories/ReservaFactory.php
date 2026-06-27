<?php

namespace Database\Factories;

use App\Models\Horario;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservaFactory extends Factory
{
    protected $model = Reserva::class;

    public function definition(): array
    {
        $metodo  = $this->faker->randomElement(['Yape', 'Efectivo']);
        $horario = Horario::factory()->reservado()->create();
        $esYape  = $metodo === 'Yape';

        return [
            'user_id'           => User::factory(),
            'horario_id'        => $horario->id,
            'metodo_pago'       => $metodo,
            'numero_operacion'  => $esYape ? $this->faker->numerify('########') : null,
            'estado_pago'       => $esYape ? Reserva::ESTADO_APROBADO : Reserva::ESTADO_PENDIENTE,
            'monto_pagado'      => $horario->tarifa?->precio ?? 15.00,
            'expira_at'         => $esYape
                ? null
                : $horario->hora_inicio->copy()->subMinutes(Reserva::MINUTOS_GRACIA),
            'codigo_validacion' => Reserva::generarCodigoValidacion(),
        ];
    }

    public function yape(): static
    {
        return $this->state([
            'metodo_pago'  => 'Yape',
            'estado_pago'  => Reserva::ESTADO_APROBADO,
            'expira_at'    => null,
        ]);
    }

    public function efectivoPendiente(): static
    {
        return $this->state([
            'metodo_pago'      => 'Efectivo',
            'estado_pago'      => Reserva::ESTADO_PENDIENTE,
            'numero_operacion' => null,
        ]);
    }

    public function anulada(): static
    {
        return $this->state([
            'estado_pago' => Reserva::ESTADO_ANULADA,
        ]);
    }
}
