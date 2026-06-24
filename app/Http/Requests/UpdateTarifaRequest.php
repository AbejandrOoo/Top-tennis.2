<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTarifaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cancha_id'   => ['required', 'exists:canchas,id'],
            'precio_hora' => ['required', 'numeric', 'min:0'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin'    => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'turno'       => ['required', 'in:Mañana,Tarde,Noche'],
            'estado'      => ['required', 'in:Activa,Inactiva'],
        ];
    }

    public function messages(): array
    {
        return [
            'cancha_id.required'   => 'Debe seleccionar una cancha.',
            'cancha_id.exists'     => 'La cancha seleccionada no existe.',
            'precio_hora.required' => 'El precio por hora es obligatorio.',
            'precio_hora.numeric'  => 'El precio debe ser un número.',
            'precio_hora.min'      => 'El precio no puede ser negativo.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'El formato de hora debe ser HH:MM.',
            'hora_fin.required'    => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'El formato de hora debe ser HH:MM.',
            'hora_fin.after'       => 'La hora de fin debe ser posterior a la hora de inicio.',
            'turno.required'       => 'El turno es obligatorio.',
            'turno.in'             => 'El turno debe ser Mañana, Tarde o Noche.',
            'estado.required'      => 'El estado es obligatorio.',
            'estado.in'            => 'El estado debe ser Activa o Inactiva.',
        ];
    }
}
