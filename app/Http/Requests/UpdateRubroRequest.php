<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRubroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'codigo_arancelario' => 'nullable|string|max:100',
            'unidad_medida' => 'required|string|max:50',
            'presentacion' => 'nullable|string|max:255',
            'precio_base' => 'required|numeric|min:0',
        ];
    }
}
