<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConductorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $conductorId = $this->route('conductor')?->id ?? $this->route('conductor');

        return [
            'nombre_completo' => 'required|string|max:255',
            'cedula' => ['required', 'string', 'max:40', Rule::unique('conductors', 'cedula')->ignore($conductorId)],
            'telefono' => 'nullable|string|max:50',
        ];
    }
}
