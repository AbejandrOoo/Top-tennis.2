<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTarifaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_tarifa' => ['required', 'string', 'max:100'],
            'precio'        => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_tarifa.required' => 'El nombre de la tarifa es obligatorio.',
            'precio.required'        => 'El precio es obligatorio.',
            'precio.numeric'         => 'El precio debe ser un número.',
            'precio.min'             => 'El precio no puede ser negativo.',
        ];
    }
}
