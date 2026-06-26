<?php

namespace App\Http\Requests;

use App\Models\Horario;
use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'horario_id'       => ['required', 'exists:horarios,id'],
            'metodo_pago'      => ['required', 'in:Yape/Plin,Efectivo'],
            // El N° de operación solo aplica (y es obligatorio) cuando se paga por Yape/Plin
            'numero_operacion' => ['nullable', 'required_if:metodo_pago,Yape/Plin', 'string', 'max:50'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $horario = Horario::find($this->horario_id);

            if (!$horario) {
                return;
            }

            // No se puede pagar una reserva que ya fue confirmada, completada o cancelada
            if ($horario->estado !== 'Reservado') {
                $validator->errors()->add('horario_id', "Esta reserva ya está {$horario->estado} y no admite un nuevo pago.");
            }

            // El cliente solo puede pagar sus propias reservas
            $user = auth()->user();
            $esStaff = in_array($user->rol, [\App\Enums\Rol::Admin, \App\Enums\Rol::Recepcionista]);
            if (!$esStaff && $horario->user_id !== $user->id) {
                $validator->errors()->add('horario_id', 'No puedes pagar una reserva que no es tuya.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'horario_id.required'       => 'No se indicó la reserva a pagar.',
            'horario_id.exists'         => 'La reserva indicada no existe.',
            'metodo_pago.required'      => 'Debe seleccionar un método de pago.',
            'metodo_pago.in'            => 'El método de pago debe ser Yape/Plin o Efectivo.',
            'numero_operacion.required_if' => 'Ingresa el número de operación de tu Yape/Plin.',
            'numero_operacion.max'      => 'El número de operación no puede superar los 50 caracteres.',
        ];
    }
}
