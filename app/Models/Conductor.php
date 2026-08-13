<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conductor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_completo',
        'cedula',
        'telefono',
    ];

    public function guias(): HasMany
    {
        return $this->hasMany(GuiaMovilizacion::class);
    }
}
