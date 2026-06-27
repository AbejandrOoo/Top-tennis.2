<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCanchaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $canchaId = $this->route('cancha')->id;

        return [
            'nombre'               => ['required', 'string', 'max:100', "unique:canchas,nombre,{$canchaId}"],
            'tipo_superficie'      => ['required', 'in:Arcilla,Sintética,Hierba,Dura'],
            'imagen'               => ['nullable', 'in:Arcilla.jpeg,CespedArtificial.jpeg,Cesped.jpeg,Dura.jpeg'],
            'estado_mantenimiento' => ['required', 'in:operativa,en_mantenimiento'],
        ];
    }

    /**
     * Regla de bloqueo: no se puede poner una cancha en mantenimiento
     * si tiene reservas futuras activas. Hay que cancelarlas/reubicarlas primero.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $cancha = $this->route('cancha');

            if ($this->estado_mantenimiento === 'en_mantenimiento') {
                $pendientes = $cancha->reservasFuturasActivas()->count();

                if ($pendientes > 0) {
                    $validator->errors()->add(
                        'estado_mantenimiento',
                        "No puedes poner esta cancha en mantenimiento: tiene {$pendientes} reserva(s) futura(s) activa(s). Cancélalas o reubícalas primero."
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'nombre.required'               => 'El nombre de la cancha es obligatorio.',
            'nombre.unique'                 => 'Ya existe una cancha con ese nombre.',
            'tipo_superficie.required'      => 'El tipo de superficie es obligatorio.',
            'tipo_superficie.in'            => 'La superficie debe ser Arcilla, Sintética, Hierba o Dura.',
            'imagen.in'                     => 'La imagen seleccionada no es válida.',
            'estado_mantenimiento.required' => 'El estado es obligatorio.',
            'estado_mantenimiento.in'       => 'Estado inválido.',
        ];
    }
}
