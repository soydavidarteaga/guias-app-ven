<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuiaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'guia_movilizacion_id',
        'rubro_id',
        'cantidad',
        'precio_unitario',
        'observacion',
    ];

    public function guia(): BelongsTo
    {
        return $this->belongsTo(GuiaMovilizacion::class, 'guia_movilizacion_id');
    }

    public function rubro(): BelongsTo
    {
        return $this->belongsTo(Rubro::class);
    }
}
