<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\Rubro;

class MacanaoMatoGrosoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = [
            ['razon_social' => 'INVERSIONES AVICOLAS, C.A.', 'rif' => 'J-29403828-0', 'direccion' => 'Carretera Km 30, vía a Perijá, Sector Jobo Alto, Municipio Maracaibo, Estado Zulia, Venezuela'],
            ['razon_social' => 'DISTRIBUIDORA Y PROCESADORA DE CARNES CA', 'rif' => 'J-30931874-8', 'direccion' => 'Avenida 10 entre calles 69 y 70, Sector Tierra Negra, Maracaibo, Estado Zulia'],
            ['razon_social' => 'AGROAVICOLA DEL LLANO', 'rif' => 'J-50563134-9', 'direccion' => 'Avenida 13 con calle 74, casa Nro. 74-14, Sector Tierra Negra, Maracaibo, Estado Zulia.'],
            ['razon_social' => 'Frigorífico Industrial Catatumbo, S.A.', 'rif' => 'J-29933048-8', 'direccion' => 'Santa Bárbara de Zulia, Estado Zulia, Venezuela'],
            ['razon_social' => 'Granja Avícola Chichi, C.A.', 'rif' => 'J-30563517-0', 'direccion' => 'Zona Industrial, Manzana 8, Parcelas 2 y 3, Maturín, Estado Monagas,'],
            ['razon_social' => 'Industrias Pollo Premium 5.8, C.A.', 'rif' => 'J-30411262-9', 'direccion' => 'Zona Industrial Los Cerritos, Carrizal, Estado Miranda, Venezuela.'],
            ['razon_social' => 'Embutidos El Drago', 'rif' => 'J-30261334-5', 'direccion' => 'Calle Sucre Nro. 46-44, Tocuyito, Estado Carabobo'],
            ['razon_social' => 'La \'J\' King Carnes y Embutidos, C.A.', 'rif' => 'J-40857371-1', 'direccion' => 'Carretera Vieja de Tocuyito, Local Nro. 2, Sector Zanjón Dulce, Municipio Libertador, Estado Carabobo.'],
            ['razon_social' => 'LP Líder Pollo C.A.', 'rif' => 'J-30713528-0', 'direccion' => 'Parcela N° 51, Parcelamiento Industrial El Recreo, Valencia, Estado Carabobo'],
            ['razon_social' => 'La Granja Avicola Rkf CA', 'rif' => 'J-31643385-4', 'direccion' => 'Urb. Industrial Río Tuy, Charallave Edo Miranda'],
            ['razon_social' => 'Avícola La Guásima, C.A.', 'rif' => 'J-07582288-9', 'direccion' => 'Carretera Vieja, vía Tocuyito, sector La Guásima, Tocuyito, Municipio Libertador, Estado Carabobo.'],
            ['razon_social' => 'Productora Occidental Porcina, C.A.', 'rif' => 'J-30469019-3', 'direccion' => 'Km. 18 vía Perijá, a 800 metros de la vía al autódromo, Maracaibo, Estado Zulia.'],
            ['razon_social' => 'Agropecuaria Nivar, C.A.', 'rif' => 'J-30005808-5', 'direccion' => 'Km 18, Vía a La Cañada, Sector Autódromo, Maracaibo, Estado Zulia.'],
            ['razon_social' => 'Agropecuaria Los Potreros, C.A.', 'rif' => 'J-30527892-0', 'direccion' => 'Carretera vía Los Claros, Parroquia Potreritos, Hacienda San Andrés, Municipio La Cañada de Urdaneta, Estado Zulia, Venezuela.'],
            ['razon_social' => 'HACIENDA LA VIÑA, COMPAÑIA ANONIMA', 'rif' => 'J-31054593-6', 'direccion' => 'CTRA KILOMETRO 56 VIA PERIJA LOCAL HACIENDA SAN PABLO NRO S/N SECTOR CAMPO BOSCAN STA- MARIA- LA HORGUETA ZULIA'],
            ['razon_social' => 'Laboratorios Reveex de Venezuela, C.A.', 'rif' => 'J-07524745-0', 'direccion' => 'Av. Antón Phillips, cruce con calle El Canal, Galpón 3-A, Complejo Industrial Reveex, Zona Industrial La Hamaca, Maracay, estado Aragua.'],
        ];

        $rubros = [
            ['nombre' => 'HARINA DE PESCADO IMPORTADA'],
            ['nombre' => 'HARINA DE PESCADO NACIONAL'],
            ['nombre' => 'ACEITE DE PESCADO'],
            ['nombre' => 'ACIDO ASCORBICO (E-300)'],
            ['nombre' => 'ACIDO CITRICO (E-330)'],
            ['nombre' => 'CARRAGENATO (E-407)'],
            ['nombre' => 'ALMIDON DE PAPA'],
            ['nombre' => 'ALMIDON DE YUCA'],
            ['nombre' => 'AISLADO DE SOYA'],
            ['nombre' => 'TEXTURIZADO DE SOYA'],
        ];

        foreach ($empresas as $empresa) {
            Empresa::firstOrCreate(
                ['rif' => $empresa['rif']],
                ['razon_social' => $empresa['razon_social'], 'direccion' => $empresa['direccion']]
            );
        }

        foreach ($rubros as $rubro) {
            Rubro::firstOrCreate(['nombre' => $rubro['nombre']]);
        }
    }
}
