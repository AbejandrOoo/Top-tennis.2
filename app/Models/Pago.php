<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'horario_id',
        'cobrado_por',
        'monto',
        'metodo_pago',
        'estado',
        'fecha_pago',
        'notas',
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto'      => 'decimal:2',
    ];

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function cobrador()
    {
        return $this->belongsTo(User::class, 'cobrado_por');
    }
}
