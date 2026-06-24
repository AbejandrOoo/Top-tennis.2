<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\Cancha;
use App\Models\Horario;
use App\Models\Tarifa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuarios de prueba para cada rol
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@toptennis.com',
            'password' => Hash::make('password'),
            'rol'      => Rol::Admin,
        ]);

        User::create([
            'name'     => 'Recepcionista',
            'email'    => 'recepcionista@toptennis.com',
            'password' => Hash::make('password'),
            'rol'      => Rol::Recepcionista,
        ]);

        User::create([
            'name'     => 'Cliente Demo',
            'email'    => 'cliente@toptennis.com',
            'password' => Hash::make('password'),
            'rol'      => Rol::Cliente,
        ]);

        // Canchas de ejemplo
        $cancha1 = Cancha::create([
            'nombre' => 'Cancha Central',
            'tipo'   => 'Arcilla',
            'estado' => 'Disponible',
        ]);

        $cancha2 = Cancha::create([
            'nombre' => 'Cancha Norte',
            'tipo'   => 'Sintética',
            'estado' => 'Disponible',
        ]);

        $cancha3 = Cancha::create([
            'nombre' => 'Cancha Sur',
            'tipo'   => 'Arcilla',
            'estado' => 'No Disponible',
        ]);

        // Tarifas para Cancha Central
        Tarifa::create([
            'cancha_id'   => $cancha1->id,
            'precio_hora' => 15000.00,
            'hora_inicio' => '07:00',
            'hora_fin'    => '12:00',
            'turno'       => 'Mañana',
            'estado'      => 'Activa',
        ]);

        Tarifa::create([
            'cancha_id'   => $cancha1->id,
            'precio_hora' => 18000.00,
            'hora_inicio' => '12:00',
            'hora_fin'    => '18:00',
            'turno'       => 'Tarde',
            'estado'      => 'Activa',
        ]);

        Tarifa::create([
            'cancha_id'   => $cancha1->id,
            'precio_hora' => 20000.00,
            'hora_inicio' => '18:00',
            'hora_fin'    => '22:00',
            'turno'       => 'Noche',
            'estado'      => 'Activa',
        ]);

        // Tarifas para Cancha Norte
        Tarifa::create([
            'cancha_id'   => $cancha2->id,
            'precio_hora' => 12000.00,
            'hora_inicio' => '07:00',
            'hora_fin'    => '12:00',
            'turno'       => 'Mañana',
            'estado'      => 'Activa',
        ]);

        $tarifaNorteTarde = Tarifa::create([
            'cancha_id'   => $cancha2->id,
            'precio_hora' => 14000.00,
            'hora_inicio' => '12:00',
            'hora_fin'    => '22:00',
            'turno'       => 'Tarde',
            'estado'      => 'Inactiva',
        ]);

        // Usuarios ya creados arriba
        $admin        = User::where('email', 'admin@toptennis.com')->first();
        $cliente      = User::where('email', 'cliente@toptennis.com')->first();
        $tarifaMañana = Tarifa::where('cancha_id', $cancha1->id)->where('turno', 'Mañana')->first();
        $tarifaTarde  = Tarifa::where('cancha_id', $cancha1->id)->where('turno', 'Tarde')->first();
        $tarifaNoche  = Tarifa::where('cancha_id', $cancha1->id)->where('turno', 'Noche')->first();
        $tarifaNorteM = Tarifa::where('cancha_id', $cancha2->id)->where('turno', 'Mañana')->first();

        // Horarios de ejemplo
        Horario::create([
            'cancha_id'   => $cancha1->id,
            'tarifa_id'   => $tarifaMañana->id,
            'user_id'     => $cliente->id,
            'fecha'       => now()->addDays(1)->toDateString(),
            'hora_inicio' => '08:00',
            'hora_fin'    => '10:00',
            'estado'      => 'Confirmado',
            'notas'       => 'Partido de práctica',
        ]);

        Horario::create([
            'cancha_id'   => $cancha1->id,
            'tarifa_id'   => $tarifaTarde->id,
            'user_id'     => $admin->id,
            'fecha'       => now()->addDays(2)->toDateString(),
            'hora_inicio' => '14:00',
            'hora_fin'    => '16:00',
            'estado'      => 'Reservado',
        ]);

        Horario::create([
            'cancha_id'   => $cancha2->id,
            'tarifa_id'   => $tarifaNorteM->id,
            'user_id'     => $cliente->id,
            'fecha'       => now()->addDays(3)->toDateString(),
            'hora_inicio' => '09:00',
            'hora_fin'    => '11:00',
            'estado'      => 'Reservado',
            'notas'       => 'Clase particular',
        ]);

        Horario::create([
            'cancha_id'   => $cancha1->id,
            'tarifa_id'   => $tarifaNoche->id,
            'user_id'     => $cliente->id,
            'fecha'       => now()->subDays(2)->toDateString(),
            'hora_inicio' => '19:00',
            'hora_fin'    => '21:00',
            'estado'      => 'Completado',
        ]);
    }
}
