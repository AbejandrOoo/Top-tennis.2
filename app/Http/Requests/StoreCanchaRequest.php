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
            'nombre'               => ['required', 'string', 'max:100', 'unique:canchas,nombre'],
            'tipo_superficie'      => ['required', 'in:Arcilla,Sintética,Hierba,Dura'],
            'imagen'               => ['nullable', 'in:Arcilla.jpeg,CespedArtificial.jpeg,Cesped.jpeg,Dura.jpeg'],
            'estado_mantenimiento' => ['required', 'in:operativa,en_mantenimiento'],
        ];
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
