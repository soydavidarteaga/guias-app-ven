<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehiculoId = $this->route('vehiculo')?->id ?? $this->route('vehiculo');

        return [
            'tipo' => 'required|string|max:100',
            'placa' => ['required', 'string', 'max:40', Rule::unique('vehiculos', 'placa')->ignore($vehiculoId)],
            'estatus' => 'required|string|in:Operativo,Mantenimiento,Inactivo',
        ];
    }
}
