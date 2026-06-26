<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarifa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre_tarifa',
        'precio',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
