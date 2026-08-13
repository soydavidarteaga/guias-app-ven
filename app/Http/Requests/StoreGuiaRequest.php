<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // En un escenario real, validar con Gates/Policies
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'empresa_origen_id' => 'required|exists:empresas,id',
            'empresa_destino_id' => 'required|exists:empresas,id',
            'conductor_id' => 'required|exists:conductors,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            
            'items' => 'required|array|min:1',
            'items.*.rubro_id' => 'required|exists:rubros,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.observacion' => 'nullable|string',
        ];
    }
}
