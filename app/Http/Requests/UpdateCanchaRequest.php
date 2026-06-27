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
            'nombre'          => ['required', 'string', 'max:100', "unique:canchas,nombre,{$canchaId}"],
            'tipo_superficie' => ['required', 'in:Arcilla,Sintética,Hierba,Dura'],
            'imagen'          => ['nullable', 'in:Arcilla.jpeg,CespedArtificial.jpeg,Cesped.jpeg,Dura.jpeg'],
            'modalidad'       => ['required', 'in:Singles,Dobles,Ambos'],
            'iluminacion'     => ['required', 'boolean'],
        ];
        // estado_mantenimiento se gestiona exclusivamente mediante el modal de
        // mantenimiento (POST /canchas/{cancha}/mantenimiento) y el botón de restaurar.
    }

    public function messages(): array
    {
        return [
            'nombre.required'          => 'El nombre de la cancha es obligatorio.',
            'nombre.unique'            => 'Ya existe una cancha con ese nombre.',
            'tipo_superficie.required' => 'El tipo de superficie es obligatorio.',
            'tipo_superficie.in'       => 'La superficie debe ser Arcilla, Sintética, Hierba o Dura.',
            'imagen.in'                => 'La imagen seleccionada no es válida.',
            'modalidad.required'       => 'La modalidad es obligatoria.',
            'modalidad.in'             => 'La modalidad debe ser Singles, Dobles o Ambos.',
            'iluminacion.required'     => 'El campo iluminación es obligatorio.',
            'iluminacion.boolean'      => 'El campo iluminación debe ser Sí o No.',
        ];
    }
}
