<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'placa',
        'estatus',
    ];

    public function guias(): HasMany
    {
        return $this->hasMany(GuiaMovilizacion::class);
    }
}
