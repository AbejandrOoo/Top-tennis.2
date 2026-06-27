<?php

namespace App\Http\Requests;

use App\Models\Horario;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'horario_id'       => ['required', 'exists:horarios,id'],
            'metodo_pago'      => ['required', 'in:Yape,Efectivo'],
            'numero_operacion' => [
                'nullable',
                'required_if:metodo_pago,Yape',
                'string',
                'max:50',
                // Anti-reuso: un mismo comprobante no puede pagar dos reservas
                'unique:reservas,numero_operacion',
            ],
        ];
    }

    /**
     * El horario debe seguir disponible, a futuro y de cancha operativa.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $horario = Horario::with('cancha')->find($this->horario_id);

            if (! $horario) {
                $validator->errors()->add('horario_id', 'El horario indicado no existe o fue eliminado.');
                return;
            }

            if ($horario->estado !== 'disponible') {
                $validator->errors()->add('horario_id', 'Ese horario ya no está disponible.');
                return;
            }

            if ($horario->hora_inicio->isPast()) {
                $validator->errors()->add('horario_id', 'No puedes reservar un horario que ya pasó.');
            }

            if (! $horario->cancha || $horario->cancha->estado_mantenimiento !== 'operativa') {
                $validator->errors()->add('horario_id', 'La cancha de ese horario está en mantenimiento.');
            }

            // No tiene sentido reservar en Efectivo si ya no hay tiempo de pagar
            // presencialmente (caducaría de inmediato).
            if (
                $this->metodo_pago === 'Efectivo'
                && $horario->hora_inicio->lte(now()->addMinutes(\App\Models\Reserva::MINUTOS_GRACIA))
            ) {
                $validator->errors()->add(
                    'metodo_pago',
                    'Faltan menos de ' . \App\Models\Reserva::MINUTOS_GRACIA . ' minutos para el inicio. Para este horario debes pagar con Yape.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'horario_id.required'          => 'No se indicó el horario a reservar.',
            'horario_id.exists'            => 'El horario indicado no existe.',
            'metodo_pago.required'         => 'Debe seleccionar un método de pago.',
            'metodo_pago.in'               => 'El método de pago debe ser Yape o Efectivo.',
            'numero_operacion.required_if' => 'Ingresa el número de operación de tu Yape.',
            'numero_operacion.max'         => 'El número de operación no puede superar los 50 caracteres.',
            'numero_operacion.unique'      => 'Ese número de operación ya fue usado en otra reserva.',
        ];
    }
}
