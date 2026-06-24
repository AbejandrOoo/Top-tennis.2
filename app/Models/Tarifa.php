<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarifa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cancha_id',
        'precio_hora',
        'hora_inicio',
        'hora_fin',
        'turno',
        'estado'
    ];

    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}