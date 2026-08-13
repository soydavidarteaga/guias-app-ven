<?php

namespace App\Services;

use App\Models\GuiaMovilizacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuiaService
{
    /**
     * Crea una nueva guía de movilización y sus ítems
     */
    public function createGuia(array $data, array $items): GuiaMovilizacion
    {
        return DB::transaction(function () use ($data, $items) {
            $nroGuia = $this->generarNroGuia();

            // Hash único codificado en base64 para URL pública de verificación QR
            $rawToken = bin2hex(random_bytes(16)) . '-' . time();
            $qrHash = urlencode(base64_encode($rawToken));

            $guia = GuiaMovilizacion::create([
                'nro_guia' => $nroGuia,
                'fecha_emision' => Carbon::now(),
                'fecha_vencimiento' => Carbon::now()->addHours(48), // Por defecto 48 horas
                'empresa_origen_id' => $data['empresa_origen_id'],
                'empresa_destino_id' => $data['empresa_destino_id'],
                'conductor_id' => $data['conductor_id'],
                'vehiculo_id' => $data['vehiculo_id'],
                'estado' => 'Emitida',
                'trazabilidad' => [],
                'qr_hash' => $qrHash,
            ]);

            foreach ($items as $item) {
                $guia->items()->create([
                    'rubro_id' => $item['rubro_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'observacion' => $item['observacion'] ?? null,
                ]);
            }

            return $guia;
        });
    }

    /**
     * Calcula el peso total de una guía
     */
    public function calcularPesoTotal(GuiaMovilizacion $guia): float
    {
        return $guia->items()->sum('cantidad');
    }

    /**
     * Actualiza el estado de la guía y añade trazabilidad
     */
    public function actualizarEstado(GuiaMovilizacion $guia, string $nuevoEstado, ?string $comentario = null): void
    {
        $trazabilidad = $guia->trazabilidad ?? [];
        $trazabilidad[] = [
            'estado' => $nuevoEstado,
            'fecha' => now()->toDateTimeString(),
            'comentario' => $comentario,
            'usuario_id' => auth()->id() ?? 'Sistema',
        ];

        $guia->update([
            'estado' => $nuevoEstado,
            'trazabilidad' => $trazabilidad,
        ]);
    }

    /**
     * Genera un número de guía único (numérico de 9 dígitos)
     */
    private function generarNroGuia(): string
    {
        return (string) rand(100000000, 999999999);
    }
}
