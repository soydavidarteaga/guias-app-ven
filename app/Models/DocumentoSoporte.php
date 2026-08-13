<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoSoporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'guia_movilizacion_id',
        'tipo_documento',
        'numero_documento',
    ];

    public function guia(): BelongsTo
    {
        return $this->belongsTo(GuiaMovilizacion::class, 'guia_movilizacion_id');
    }
}
