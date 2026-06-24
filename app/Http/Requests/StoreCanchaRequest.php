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
            'nombre'    => ['required', 'string', 'max:100', 'unique:canchas,nombre'],
            'tipo'      => ['required', 'in:Arcilla,Sintética,Hierba,Dura'],
            'modalidad' => ['required', 'in:Singles,Dobles'],
            'capacidad' => ['required', 'integer', 'min:1', 'max:8'],
            'estado'    => ['required', 'in:Disponible,No Disponible,Bloqueada'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'    => 'El nombre de la cancha es obligatorio.',
            'nombre.unique'      => 'Ya existe una cancha con ese nombre.',
            'tipo.required'      => 'El tipo de superficie es obligatorio.',
            'tipo.in'            => 'El tipo debe ser Arcilla, Sintética, Hierba o Dura.',
            'modalidad.required' => 'La modalidad es obligatoria.',
            'modalidad.in'       => 'La modalidad debe ser Singles o Dobles.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.integer'  => 'La capacidad debe ser un número entero.',
            'estado.required'    => 'El estado es obligatorio.',
            'estado.in'          => 'Estado inválido.',
        ];
    }
}
