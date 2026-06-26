<?php

namespace App\Http\Requests;

use App\Models\Horario;
use Illuminate\Foundation\Http\FormRequest;

class StoreHorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cancha_id'   => ['required', 'exists:canchas,id'],
            'tarifa_id'   => ['required', 'exists:tarifas,id'],
            'hora_inicio' => ['required', 'date', 'after_or_equal:now'],
            'hora_fin'    => ['required', 'date', 'after:hora_inicio'],
        ];
    }

    /**
     * Regla de cruce: no puede existir otro horario de la misma cancha
     * cuyo rango [inicio, fin) se superponga con el nuevo.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->cancha_id || ! $this->hora_inicio || ! $this->hora_fin) {
                return;
            }

            $cruce = Horario::where('cancha_id', $this->cancha_id)
                ->where('hora_inicio', '<', $this->hora_fin)
                ->where('hora_fin', '>', $this->hora_inicio)
                ->exists();

            if ($cruce) {
                $validator->errors()->add(
                    'hora_inicio',
                    'Ya existe un horario para esta cancha que se cruza con ese rango de fecha y hora.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'cancha_id.required'   => 'Debe seleccionar una cancha.',
            'cancha_id.exists'     => 'La cancha seleccionada no existe.',
            'tarifa_id.required'   => 'Debe seleccionar una tarifa.',
            'tarifa_id.exists'     => 'La tarifa seleccionada no existe.',
            'hora_inicio.required' => 'La fecha y hora de inicio es obligatoria.',
            'hora_inicio.after_or_equal' => 'El horario no puede empezar en el pasado.',
            'hora_fin.required'    => 'La fecha y hora de fin es obligatoria.',
            'hora_fin.after'       => 'La hora de fin debe ser posterior a la de inicio.',
        ];
    }
}
