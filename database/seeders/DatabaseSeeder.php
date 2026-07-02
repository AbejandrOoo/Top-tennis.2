<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\Cancha;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Tarifa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Usuarios =====
        $admin = User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@toptennis.com',
            'password' => Hash::make('password'),
            'rol'      => Rol::Admin,
        ]);

        $cliente = User::create([
            'name'     => 'Cliente Demo',
            'email'    => 'cliente@toptennis.com',
            'password' => Hash::make('password'),
            'rol'      => Rol::Cliente,
        ]);

        // ===== 4 Canchas =====
        $central = Cancha::create([
            'nombre'               => 'Cancha Central',
            'tipo_superficie'      => 'Arcilla',
            'imagen'               => 'Arcilla.jpeg',
            'modalidad'            => 'Singles',
            'iluminacion'          => true,
            'estado_mantenimiento' => 'operativa',
        ]);

        $norte = Cancha::create([
            'nombre'               => 'Cancha Norte',
            'tipo_superficie'      => 'Sintética',
            'imagen'               => 'CespedArtificial.jpeg',
            'modalidad'            => 'Dobles',
            'iluminacion'          => true,
            'estado_mantenimiento' => 'operativa',
        ]);

        $este = Cancha::create([
            'nombre'               => 'Cancha Este',
            'tipo_superficie'      => 'Dura',
            'imagen'               => 'Dura.jpeg',
            'modalidad'            => 'Ambos',
            'iluminacion'          => true,
            'estado_mantenimiento' => 'operativa',
        ]);

        Cancha::create([
            'nombre'               => 'Cancha Sur',
            'tipo_superficie'      => 'Arcilla',
            'imagen'               => 'Arcilla.jpeg',
            'modalidad'            => 'Singles',
            'iluminacion'          => false,
            'estado_mantenimiento'  => 'en_mantenimiento',
            'motivo_mantenimiento'  => 'Reparación de superficie',
            'inicio_mantenimiento'  => now(),
            'fin_mantenimiento'     => now()->addDays(7),
        ]);

        // ===== Tarifas =====
        $tarifaDia   = Tarifa::create(['nombre_tarifa' => 'Tarifa Día',   'precio' => 50.00]);
        $tarifaNoche = Tarifa::create(['nombre_tarifa' => 'Tarifa Noche', 'precio' => 60.00]);

        // ===== Horarios: 6 AM – 10 PM, 7 días, todas las canchas operativas =====
        $canchasOperativas = [$central, $norte, $este];
        $hoy = Carbon::today();

        $primerHorario  = null;
        $segundoHorario = null;

        for ($dia = 0; $dia < 7; $dia++) {
            $fecha = $hoy->copy()->addDays($dia);

            foreach ($canchasOperativas as $cancha) {
                for ($hora = 6; $hora < 22; $hora++) {
                    $inicio = $fecha->copy()->setTime($hora, 0);
                    $fin    = $fecha->copy()->setTime($hora + 1, 0);

                    $tarifa = $hora < 18 ? $tarifaDia : $tarifaNoche;

                    $horario = Horario::create([
                        'cancha_id'   => $cancha->id,
                        'tarifa_id'   => $tarifa->id,
                        'hora_inicio' => $inicio,
                        'hora_fin'    => $fin,
                        'estado'      => 'disponible',
                    ]);

                    if ($dia === 1 && $cancha->id === $central->id && $hora === 9 && ! $primerHorario) {
                        $primerHorario = $horario;
                    }
                    if ($dia === 1 && $cancha->id === $central->id && $hora === 15 && ! $segundoHorario) {
                        $segundoHorario = $horario;
                    }
                }
            }
        }

        // ===== Reservas demo =====
        if ($primerHorario) {
            Reserva::create([
                'user_id'           => $cliente->id,
                'horario_id'        => $primerHorario->id,
                'metodo_pago'       => 'Yape',
                'numero_operacion'  => '01234567',
                'estado_pago'       => Reserva::ESTADO_APROBADO,
                'monto_pagado'      => $primerHorario->tarifa->precio,
                'codigo_validacion' => Reserva::generarCodigoValidacion(),
            ]);
            $primerHorario->update(['estado' => 'reservado']);
        }

        if ($segundoHorario) {
            Reserva::create([
                'user_id'           => $cliente->id,
                'horario_id'        => $segundoHorario->id,
                'metodo_pago'       => 'Efectivo',
                'estado_pago'       => Reserva::ESTADO_PENDIENTE,
                'monto_pagado'      => $segundoHorario->tarifa->precio,
                'expira_at'         => $segundoHorario->hora_inicio->copy()->subMinutes(Reserva::MINUTOS_GRACIA),
                'codigo_validacion' => Reserva::generarCodigoValidacion(),
            ]);
            $segundoHorario->update(['estado' => 'reservado']);
        }
    }
}
