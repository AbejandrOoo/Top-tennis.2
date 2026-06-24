<?php

namespace App\Http\Requests;

use App\Models\Horario;
use App\Models\Tarifa;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHorarioRequest extends FormRequest
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
            'fecha'       => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin'    => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'estado'      => ['required', 'in:Reservado,Confirmado,Cancelado,Completado'],
            'notas'       => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $horarioActual = $this->route('horario');

            // La tarifa debe pertenecer a la cancha seleccionada
            $tarifa = Tarifa::find($this->tarifa_id);
            if ($tarifa && $tarifa->cancha_id != $this->cancha_id) {
                $validator->errors()->add('tarifa_id', 'La tarifa no corresponde a la cancha seleccionada.');
            }

            // Solapamiento ignorando el horario que se está editando
            $solapamiento = Horario::where('cancha_id', $this->cancha_id)
                ->where('fecha', $this->fecha)
                ->where('id', '!=', $horarioActual->id)
                ->whereNotIn('estado', ['Cancelado'])
                ->where('hora_inicio', '<', $this->hora_fin)
                ->where('hora_fin', '>', $this->hora_inicio)
                ->exists();

            if ($solapamiento) {
                $validator->errors()->add('hora_inicio', 'Ya existe un horario reservado en ese rango de horas para esta cancha.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'cancha_id.required'      => 'Debe seleccionar una cancha.',
            'tarifa_id.required'      => 'Debe seleccionar una tarifa.',
            'fecha.required'          => 'La fecha es obligatoria.',
            'hora_inicio.required'    => 'La hora de inicio es obligatoria.',
            'hora_fin.required'       => 'La hora de fin es obligatoria.',
            'hora_fin.after'          => 'La hora de fin debe ser posterior a la hora de inicio.',
            'estado.required'         => 'El estado es obligatorio.',
            'estado.in'               => 'Estado inválido.',
            'notas.max'               => 'Las notas no pueden superar los 500 caracteres.',
        ];
    }
}
