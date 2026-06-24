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
        'tipo',
        'estado'
    ];

    public function tarifas()
    {
        return $this->hasMany(Tarifa::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}