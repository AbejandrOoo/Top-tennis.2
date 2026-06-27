<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// MODELO HORARIO: ES LA "TABLA PUENTE" ENTRE CANCHA Y TARIFA
// REPRESENTA UN SLOT DE 1 HORA RESERVABLE (EJ: CANCHA 1, 08:00-09:00, TARIFA DIA)
// ESTADOS: 'disponible' (LIBRE PARA RESERVAR) O 'reservado' (YA TIENE RESERVA)
// RELACIONES: PERTENECE A UNA CANCHA (N:1) Y A UNA TARIFA (N:1), TIENE UNA RESERVA (1:1)
class Horario extends Model
{
    use HasFactory, SoftDeletes;

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
    // SCOPE "RESERVABLES": FILTRA SOLO LOS HORARIOS QUE EL CLIENTE PUEDE RESERVAR
    // CONDICIONES: ESTADO DISPONIBLE + HORA FUTURA + CANCHA OPERATIVA + TARIFA EXISTENTE
    // SE USA EN: ReservaController::disponibles() Y ReservaController::crearManual()
    public function scopeReservables($query)
    {
        return $query->where('estado', 'disponible')
            ->where('hora_inicio', '>', now())
            ->whereHas('cancha', fn ($c) => $c->where('estado_mantenimiento', 'operativa'))
            ->whereHas('tarifa');
    }
}
