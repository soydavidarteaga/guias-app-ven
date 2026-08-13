<?php

namespace App\Models;

use Database\Factories\EmpresaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    /** @use HasFactory<EmpresaFactory> */
    use HasFactory;

    protected $fillable = [
        'razon_social',
        'rif',
        'codigo_sica',
        'persona_autorizada',
        'telefonos',
        'estado',
        'ciudad',
        'parroquia',
        'direccion',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function guiasComoOrigen(): HasMany
    {
        return $this->hasMany(GuiaMovilizacion::class, 'empresa_origen_id');
    }

    public function guiasComoDestino(): HasMany
    {
        return $this->hasMany(GuiaMovilizacion::class, 'empresa_destino_id');
    }
}
