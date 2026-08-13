<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = $this->route('empresa')?->id ?? $this->route('empresa');

        return [
            'razon_social' => 'required|string|max:255',
            'rif' => ['required', 'string', 'max:40', Rule::unique('empresas', 'rif')->ignore($empresaId)],
            'codigo_sica' => ['nullable', 'string', 'max:40', Rule::unique('empresas', 'codigo_sica')->ignore($empresaId)],
            'persona_autorizada' => 'nullable|string|max:255',
            'telefonos' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'parroquia' => 'nullable|string|max:100',
            'direccion' => 'nullable|string',
        ];
    }
}
