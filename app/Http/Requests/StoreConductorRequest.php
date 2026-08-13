<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConductorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_completo' => 'required|string|max:255',
            'cedula' => 'required|string|max:40|unique:conductors,cedula',
            'telefono' => 'nullable|string|max:50',
        ];
    }
}
