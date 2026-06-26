<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cancha_id',
        'tarifa_id',
        'hora_inicio',
        'hora_fin',
        'estado',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime',
        'hora_fin'    => 'datetime',
    ];

    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }

    public function tarifa()
    {
        return $this->belongsTo(Tarifa::class);
    }

    public function reserva()
    {
        return $this->hasOne(Reserva::class);
    }

    /**
     * Horarios que el cliente puede reservar:
     * disponibles, a futuro y de canchas operativas.
     */
    public function scopeReservables($query)
    {
        return $query->where('estado', 'disponible')
            ->where('hora_inicio', '>', now())
            ->whereHas('cancha', fn ($c) => $c->where('estado_mantenimiento', 'operativa'));
    }
}
