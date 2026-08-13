<?php

namespace App\Models;

use Database\Factories\RubroFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rubro extends Model
{
    /** @use HasFactory<RubroFactory> */
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo_arancelario',
        'unidad_medida',
        'presentacion',
        'precio_base',
    ];
}
