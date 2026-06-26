<?php

namespace App\Http\Requests;

use App\Models\Horario;
use App\Models\Tarifa;
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
            'fecha'       => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin'    => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'notas'        => ['nullable', 'string', 'max:500'],
            'metodo_pago'  => ['nullable', 'in:Efectivo,Tarjeta,Transferencia,Otro'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // La tarifa debe pertenecer a la cancha seleccionada
            $tarifa = Tarifa::find($this->tarifa_id);
            if ($tarifa && (int) $tarifa->cancha_id !== (int) $this->cancha_id) {
                $validator->errors()->add('tarifa_id', 'La tarifa no corresponde a la cancha seleccionada.');
            }

            // La tarifa debe estar Activa
            if ($tarifa && $tarifa->estado !== 'Activa') {
                $validator->errors()->add('tarifa_id', 'La tarifa seleccionada no está activa.');
            }

            // No puede haber solapamiento de horarios en la misma cancha y fecha
            $solapamiento = Horario::where('cancha_id', $this->cancha_id)
                ->where('fecha', $this->fecha)
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
            'cancha_id.exists'        => 'La cancha seleccionada no existe.',
            'tarifa_id.required'      => 'Debe seleccionar una tarifa.',
            'tarifa_id.exists'        => 'La tarifa seleccionada no existe.',
            'fecha.required'          => 'La fecha es obligatoria.',
            'fecha.after_or_equal'    => 'No se pueden crear horarios en fechas pasadas.',
            'hora_inicio.required'    => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'Formato de hora inválido (HH:MM).',
            'hora_fin.required'       => 'La hora de fin es obligatoria.',
            'hora_fin.date_format'    => 'Formato de hora inválido (HH:MM).',
            'hora_fin.after'          => 'La hora de fin debe ser posterior a la hora de inicio.',
            'notas.max'               => 'Las notas no pueden superar los 500 caracteres.',
        ];
    }
}
