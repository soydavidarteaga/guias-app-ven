<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => 'required|string|max:100',
            'placa' => 'required|string|max:40|unique:vehiculos,placa',
            'estatus' => 'required|string|in:Operativo,Mantenimiento,Inactivo',
        ];
    }
}
