<?php

namespace App\Models;

use Database\Factories\GuiaMovilizacionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuiaMovilizacion extends Model
{
    /** @use HasFactory<GuiaMovilizacionFactory> */
    use HasFactory;

    protected $fillable = [
        'nro_guia',
        'fecha_emision',
        'fecha_vencimiento',
        'empresa_origen_id',
        'empresa_destino_id',
        'conductor_id',
        'vehiculo_id',
        'estado',
        'trazabilidad',
        'qr_hash',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'trazabilidad' => 'array',
    ];

    public function empresaOrigen(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_origen_id');
    }

    public function empresaDestino(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_destino_id');
    }

    public function conductor(): BelongsTo
    {
        return $this->belongsTo(Conductor::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GuiaItem::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoSoporte::class);
    }
}
