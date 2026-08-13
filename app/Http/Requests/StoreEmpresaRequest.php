<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'razon_social' => 'required|string|max:255',
            'rif' => 'required|string|max:40|unique:empresas,rif',
            'codigo_sica' => 'nullable|string|max:40|unique:empresas,codigo_sica',
            'persona_autorizada' => 'nullable|string|max:255',
            'telefonos' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'parroquia' => 'nullable|string|max:100',
            'direccion' => 'nullable|string',
        ];
    }
}
