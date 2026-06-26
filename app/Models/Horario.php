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
        'user_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'notas',
        'metodo_pago',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }

    public function tarifa()
    {
        return $this->belongsTo(Tarifa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
