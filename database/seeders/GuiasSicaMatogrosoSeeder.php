<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Empresa;
use App\Models\Rubro;
use App\Models\Conductor;
use App\Models\Vehiculo;
use App\Models\GuiaMovilizacion;
use App\Models\GuiaItem;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GuiasSicaMatogrosoSeeder extends Seeder
{
    public function run(): void
    {
        $json = File::get(storage_path('app/guias_matogroso.json'));
        $data = json_decode($json, true);

        $counter = 1;

        foreach ($data as $row) {
            if (empty($row['empresa_origen']))
                continue;

            $rifOrigen = str_contains(strtoupper($row['empresa_origen']), 'MATO GROSO') ? 'J-41201288-6' : 'J-00000000-0';
            $direccionOrigen = 'AV DOMINGO OLAVARRIA CON CALLE NORTE - SUR 1, PARCELA 3-4 LOCAL NRO G-09 SECTOR ZONA INDUSTRIAL MUNICIPAL SUR VALENCIA CARABOBO ZONA POSTAL 2003';

            $origen = Empresa::firstOrCreate(
                ['razon_social' => $row['empresa_origen']],
                ['rif' => $rifOrigen, 'direccion' => $direccionOrigen]
            );

            $destino = Empresa::firstOrCreate(
                ['rif' => empty($row['rif_destino']) ? 'J-00000000-1' : $row['rif_destino']],
                ['razon_social' => $row['empresa_destino'], 'direccion' => $row['direccion_destino'] ?? '']
            );

            $conductor = Conductor::firstOrCreate(
                ['cedula' => empty($row['ci_conductor']) ? '00000000' : $row['ci_conductor']],
                ['nombre_completo' => $row['conductor'] ?? 'Desconocido', 'telefono' => '']
            );

            // Vehiculo comes as "PLACA: A68AG0N"
            $placa = str_replace('PLACA: ', '', $row['vehiculo']);
            $vehiculo = Vehiculo::firstOrCreate(
                ['placa' => empty($placa) ? 'S/P' : $placa],
                ['tipo' => 'Camión', 'estatus' => 'Activo']
            );

            $rubro = Rubro::firstOrCreate(
                ['nombre' => $row['rubro']],
                ['codigo_arancelario' => '0000']
            );

            // Parse Date (comes as dd/mm/yyyy or yyyy-mm-dd depending on pandas)
            try {
                $fecha = Carbon::createFromFormat('d/m/Y', $row['fecha_emision']);
            } catch (\Exception $e) {
                try {
                    $fecha = Carbon::parse($row['fecha_emision']);
                } catch (\Exception $e) {
                    $fecha = now();
                }
            }

            $nota_entrega = empty($row['nota_entrega']) ? 'NE-AUTO-' . uniqid() : $row['nota_entrega'];

            $guia = GuiaMovilizacion::where('documentos_soporte', $nota_entrega)->first();

            if (!$guia) {
                do {
                    $nro_guia = (string) random_int(100000000, 999999999);
                } while (GuiaMovilizacion::where('nro_guia', $nro_guia)->exists());
                $guia = GuiaMovilizacion::create([
                    'nro_guia' => $nro_guia,
                    'fecha_emision' => $fecha,
                    'fecha_vencimiento' => $fecha->copy()->addDays(4),
                    'empresa_origen_id' => $origen->id,
                    'empresa_destino_id' => $destino->id,
                    'conductor_id' => $conductor->id,
                    'vehiculo_id' => $vehiculo->id,
                    'estado' => 'Completada',
                    'documentos_soporte' => $nota_entrega,
                ]);
                $counter++;
            }

            GuiaItem::create([
                'guia_movilizacion_id' => $guia->id,
                'rubro_id' => $rubro->id,
                'cantidad' => (float) $row['cant_tn'],
                'precio_unitario' => 0,
            ]);
        }
    }
}
