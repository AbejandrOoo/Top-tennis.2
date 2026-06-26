<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cancha extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'tipo_superficie',
        'estado_mantenimiento',
    ];

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    /**
     * Reservas futuras todavía activas (horario reservado con inicio en el futuro).
     * Sirve para la regla de bloqueo por mantenimiento.
     */
    public function reservasFuturasActivas()
    {
        return Reserva::whereHas('horario', function ($q) {
            $q->where('cancha_id', $this->id)
              ->where('estado', 'reservado')
              ->where('hora_inicio', '>', now());
        });
    }

    public function estaOperativa(): bool
    {
        return $this->estado_mantenimiento === 'operativa';
    }
}
