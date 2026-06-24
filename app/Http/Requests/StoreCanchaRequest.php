<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCanchaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100', 'unique:canchas,nombre'],
            'tipo'   => ['required', 'in:Arcilla,Sintética'],
            'estado' => ['required', 'in:Disponible,No Disponible'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la cancha es obligatorio.',
            'nombre.unique'   => 'Ya existe una cancha con ese nombre.',
            'tipo.required'   => 'El tipo de superficie es obligatorio.',
            'tipo.in'         => 'El tipo debe ser Arcilla o Sintética.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in'       => 'El estado debe ser Disponible o No Disponible.',
        ];
    }
}
