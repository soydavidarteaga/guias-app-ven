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
        return true;
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
            'fecha_emision' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'documentos_soporte' => 'nullable|string|max:1000',
            'observacion' => 'nullable|string|max:1000',
            
            'items' => 'required|array|min:1',
            'items.*.rubro_id' => 'required|exists:rubros,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.observacion' => 'nullable|string',
        ];
    }
}
