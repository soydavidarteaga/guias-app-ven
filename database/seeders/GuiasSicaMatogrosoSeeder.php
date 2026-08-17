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
        $xlsx = \Shuchkin\SimpleXLSX::parse(base_path('data/Todas_Notas_SICA_Verificado_Definitivo.xlsx'));

        if (!$xlsx) {
            echo \Shuchkin\SimpleXLSX::parseError();
            return;
        }

        $rows = $xlsx->rows();
        $header = array_shift($rows);

        $data = [];
        foreach ($rows as $row) {
            $row = array_pad($row, count($header), null);
            $data[] = array_combine($header, $row);
        }

        $counter = 1;

        foreach ($data as $row) {
            if (empty($row['Empresa Origen']))
                continue;

            $rifOrigen = str_contains(strtoupper($row['Empresa Origen']), 'MATO GROSO') ? 'J-41201288-6' : 'J-00000000-0';
            $direccionOrigen = 'AV DOMINGO OLAVARRIA CON CALLE NORTE - SUR 1, PARCELA 3-4 LOCAL NRO G-09 SECTOR ZONA INDUSTRIAL MUNICIPAL SUR VALENCIA CARABOBO ZONA POSTAL 2003';

            $origen = Empresa::firstOrCreate(
                ['razon_social' => $row['Empresa Origen']],
                [
                    'rif' => $rifOrigen,
                    'direccion' => $direccionOrigen,
                    'estado' => 'CARABOBO',
                    'ciudad' => 'VALENCIA',
                    'parroquia' => 'URBANA RAFAEL URDANETA',
                    'telefonos' => '0424-2610767 0424-2610767',
                    'persona_autorizada' => 'ANTONIO GRATEROL',
                    'codigo_sica' => '715118'
                ]
            );

            $destino = Empresa::firstOrCreate(
                ['rif' => empty($row['RIF Destino']) || $row['RIF Destino'] == '-' ? 'J-00000000-1' : $row['RIF Destino']],
                [
                    'razon_social' => empty($row['Empresa Destino']) ? '-' : $row['Empresa Destino'],
                    'direccion' => empty($row['Dirección Destino']) ? '-' : $row['Dirección Destino'],
                    'estado' => empty($row['Estado']) ? '-' : $row['Estado'],
                    'ciudad' => empty($row['Municipio']) ? '-' : $row['Municipio'],
                    'parroquia' => empty($row['Parroquia']) ? '-' : $row['Parroquia'],
                    'telefonos' => empty($row['Teléfono']) ? '-' : $row['Teléfono'],
                    'persona_autorizada' => empty($row['Persona de Contacto']) ? '-' : $row['Persona de Contacto'],
                ]
            );

            $conductor = Conductor::firstOrCreate(
                ['cedula' => empty($row['C.I. Conductor']) || $row['C.I. Conductor'] == '-' ? '00000000' : $row['C.I. Conductor']],
                ['nombre_completo' => $row['Conductor'] ?? '-', 'telefono' => '-']
            );

            $placa = str_replace('PLACA: ', '', $row['Vehículo'] ?? '');
            $vehiculo = Vehiculo::firstOrCreate(
                ['placa' => empty($placa) || $placa == '-' ? 'S/P' : $placa],
                ['tipo' => 'Camión', 'estatus' => 'OPERATIVO']
            );

            $codigo_arancelario = empty($row['Cod. Arancelario']) || $row['Cod. Arancelario'] == '-' ? '-' : $row['Cod. Arancelario'];
            $rubro = Rubro::firstOrCreate(
                ['nombre' => $row['Rubro']],
                ['codigo_arancelario' => $codigo_arancelario, 'presentacion' => '-']
            );

            // Parse Date
            $fecha_str = $row['Fecha Emisión'] ?? '';
            try {
                $fecha = Carbon::createFromFormat('d/m/Y', $fecha_str);
            } catch (\Exception $e) {
                try {
                    $fecha = Carbon::parse($fecha_str);
                } catch (\Exception $e) {
                    $fecha = now();
                }
            }

            $nota_entrega = empty($row['Nota Entrega / Factura']) || $row['Nota Entrega / Factura'] == '-' ? 'NE-AUTO-' . uniqid() : $row['Nota Entrega / Factura'];
            $nro_guia_sica = empty($row['Nro. Guia SICA']) || $row['Nro. Guia SICA'] == '-' ? null : $row['Nro. Guia SICA'];

            $guia = GuiaMovilizacion::where('documentos_soporte', $nota_entrega)->first();

            if (!$guia) {
                if ($nro_guia_sica) {
                    $nro_guia = $nro_guia_sica;
                } else {
                    do {
                        $nro_guia = (string) random_int(100000000, 999999999);
                    } while (GuiaMovilizacion::where('nro_guia', $nro_guia)->exists());
                }

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
                    'observacion' => '-',
                ]);
                $counter++;
            }

            $cant_tn = $row['Cant (TN)'] ?? 0;
            if (is_string($cant_tn)) {
                $cant_tn = str_replace(',', '.', $cant_tn);
            }

            GuiaItem::create([
                'guia_movilizacion_id' => $guia->id,
                'rubro_id' => $rubro->id,
                'cantidad' => (float) $cant_tn,
                'precio_unitario' => 0,
            ]);
        }
    }
}
