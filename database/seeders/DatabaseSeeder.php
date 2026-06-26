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

        User::create([
            'name'     => 'Recepcionista',
            'email'    => 'recepcionista@toptennis.com',
            'password' => Hash::make('password'),
            'rol'      => Rol::Recepcionista,
        ]);

        $cliente = User::create([
            'name'     => 'Cliente Demo',
            'email'    => 'cliente@toptennis.com',
            'password' => Hash::make('password'),
            'rol'      => Rol::Cliente,
        ]);

        // ===== Canchas =====
        $central = Cancha::create([
            'nombre'               => 'Cancha Central',
            'tipo_superficie'      => 'Arcilla',
            'estado_mantenimiento' => 'operativa',
        ]);

        $norte = Cancha::create([
            'nombre'               => 'Cancha Norte',
            'tipo_superficie'      => 'Sintética',
            'estado_mantenimiento' => 'operativa',
        ]);

        Cancha::create([
            'nombre'               => 'Cancha Sur',
            'tipo_superficie'      => 'Arcilla',
            'estado_mantenimiento' => 'en_mantenimiento',
        ]);

        // ===== Tarifas =====
        $manana = Tarifa::create(['nombre_tarifa' => 'Tarifa Mañana', 'precio' => 15.00]);
        $tarde  = Tarifa::create(['nombre_tarifa' => 'Tarifa Tarde',  'precio' => 18.00]);
        $noche  = Tarifa::create(['nombre_tarifa' => 'Hora Punta (Noche)', 'precio' => 22.00]);

        // ===== Horarios (slots) a futuro =====
        $hoy = Carbon::today();

        $slots = [
            [$central, $manana, 1, '08:00', '09:00', 'disponible'],
            [$central, $tarde,  1, '15:00', '16:00', 'disponible'],
            [$central, $noche,  2, '19:00', '20:00', 'disponible'],
            [$norte,   $manana, 1, '09:00', '10:00', 'disponible'],
            [$norte,   $tarde,  2, '16:00', '17:00', 'disponible'],
            [$norte,   $noche,  3, '20:00', '21:00', 'disponible'],
        ];

        $horariosCreados = [];
        foreach ($slots as [$cancha, $tarifa, $diasAdelante, $ini, $fin, $estado]) {
            $fecha = $hoy->copy()->addDays($diasAdelante);
            $horariosCreados[] = Horario::create([
                'cancha_id'   => $cancha->id,
                'tarifa_id'   => $tarifa->id,
                'hora_inicio' => $fecha->copy()->setTimeFromTimeString($ini),
                'hora_fin'    => $fecha->copy()->setTimeFromTimeString($fin),
                'estado'      => $estado,
            ]);
        }

        // ===== Reserva 1: Yape (aprobada) sobre el primer slot =====
        $primero = $horariosCreados[0];
        Reserva::create([
            'user_id'           => $cliente->id,
            'horario_id'        => $primero->id,
            'metodo_pago'       => 'Yape',
            'numero_operacion'  => '01234567',
            'estado_pago'       => Reserva::ESTADO_APROBADO,
            'codigo_validacion' => Reserva::generarCodigoValidacion(),
        ]);
        $primero->update(['estado' => 'reservado']);

        // ===== Reserva 2: Efectivo (pendiente) con plazo de pago a 30 min =====
        $segundo = $horariosCreados[1];
        Reserva::create([
            'user_id'           => $cliente->id,
            'horario_id'        => $segundo->id,
            'metodo_pago'       => 'Efectivo',
            'estado_pago'       => Reserva::ESTADO_PENDIENTE,
            'expira_at'         => $segundo->hora_inicio->copy()->subMinutes(Reserva::MINUTOS_GRACIA),
            'codigo_validacion' => Reserva::generarCodigoValidacion(),
        ]);
        $segundo->update(['estado' => 'reservado']);
    }
}
