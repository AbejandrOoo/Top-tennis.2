<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// MODELO TARIFA: REGLA DE PRECIO INDEPENDIENTE DE LA CANCHA
// ARQUITECTURA DE 3 CAPAS: CANCHA -> HORARIO <- TARIFA (TABLA PUENTE)
// LOS PRECIOS ESTAN EN TARIFAS, NO EN HORARIOS NI EN CANCHAS
// ESTO PERMITE CAMBIAR PRECIOS SIN TOCAR LOS HORARIOS EXISTENTES
// USA SOFTDELETES PARA NO PERDER EL HISTORIAL FINANCIERO
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
