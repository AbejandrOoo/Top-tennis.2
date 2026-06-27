<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// MODELO CANCHA: REPRESENTA UNA CANCHA FISICA DEL CLUB
// USA SOFTDELETES PARA BORRADO LOGICO (NO SE ELIMINA DE LA BD, SE MARCA COMO ELIMINADA)
// RELACION: UNA CANCHA TIENE MUCHOS HORARIOS (1:N)
// ATRIBUTOS: nombre, tipo_superficie, imagen, modalidad, iluminacion, estado_mantenimiento
class Cancha extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'tipo_superficie',
        'imagen',
        'modalidad',
        'iluminacion',
        'estado_mantenimiento',
        'motivo_mantenimiento',
        'inicio_mantenimiento',
        'fin_mantenimiento',
    ];

    protected $casts = [
        'iluminacion'          => 'boolean',
        'inicio_mantenimiento' => 'datetime',
        'fin_mantenimiento'    => 'datetime',
    ];

    public const IMAGENES = [
        'Arcilla'   => 'Arcilla.jpeg',
        'Sintética' => 'CespedArtificial.jpeg',
        'Hierba'    => 'Cesped.jpeg',
        'Dura'      => 'Dura.jpeg',
    ];

    public const MODALIDADES   = ['Singles', 'Dobles', 'Ambos'];
    public const SUPERFICIES   = ['Arcilla', 'Sintética', 'Hierba', 'Dura'];

    public function imagenUrl(): string
    {
        $archivo = $this->imagen ?? self::IMAGENES[$this->tipo_superficie] ?? 'Arcilla.jpeg';
        return asset('images/' . $archivo);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function reservasFuturasActivas()
    {
        return Reserva::whereHas('horario', function ($q) {
            $q->where('cancha_id', $this->id)
              ->where('estado', 'reservado')
              ->where('hora_inicio', '>', now());
        });
    }

    // METODO DE CONSULTA: VERIFICA SI LA CANCHA ESTA DISPONIBLE PARA JUGAR
    public function estaOperativa(): bool
    {
        return $this->estado_mantenimiento === 'operativa';
    }

    /**
     * Restaura automáticamente las canchas cuyo fin_mantenimiento ya pasó.
     * Llamar en modo "lazy" al inicio de disponibles() y canchas.index.
     */
    // METODO ESTATICO "LAZY": SE EJECUTA AL CARGAR VISTAS PARA RESTAURAR
    // AUTOMATICAMENTE CANCHAS CUYO PERIODO DE MANTENIMIENTO YA TERMINO
    public static function restaurarVencidas(): void
    {
        static::where('estado_mantenimiento', 'en_mantenimiento')
            ->whereNotNull('fin_mantenimiento')
            ->where('fin_mantenimiento', '<=', now())
            ->update([
                'estado_mantenimiento'  => 'operativa',
                'motivo_mantenimiento'  => null,
                'inicio_mantenimiento'  => null,
                'fin_mantenimiento'     => null,
            ]);
    }
}
