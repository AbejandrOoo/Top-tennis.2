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
        'imagen',
        'estado_mantenimiento',
    ];

    // Imágenes disponibles en public/images/ por tipo de superficie
    public const IMAGENES = [
        'Arcilla'   => 'Arcilla.jpeg',
        'Sintética' => 'CespedArtificial.jpeg',
        'Hierba'    => 'Cesped.jpeg',
        'Dura'      => 'Dura.jpeg',
    ];

    /**
     * URL pública de la imagen de la cancha.
     * Usa la imagen asignada; si no tiene, toma la del tipo de superficie.
     */
    public function imagenUrl(): string
    {
        $archivo = $this->imagen ?? self::IMAGENES[$this->tipo_superficie] ?? 'Arcilla.jpeg';
        return asset('images/' . $archivo);
    }

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
