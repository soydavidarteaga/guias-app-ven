<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Rubro;
use App\Models\Conductor;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $origen = Empresa::create([
            'razon_social' => 'Alimentos Polar C.A.',
            'rif' => 'J-00001111-2',
            'codigo_sica' => '715118',
            'persona_autorizada' => 'Juan Perez',
            'telefonos' => '0414-1234567',
            'estado' => 'Carabobo',
            'ciudad' => 'Valencia',
            'parroquia' => 'San Jose',
            'direccion' => 'Zona Industrial Carabobo',
        ]);

        $destino = Empresa::create([
            'razon_social' => 'Supermercados Central Madeirense',
            'rif' => 'J-12345678-9',
            'codigo_sica' => '992357',
            'persona_autorizada' => 'Maria Gonzalez',
            'telefonos' => '0212-9876543',
            'estado' => 'Distrito Capital',
            'ciudad' => 'Caracas',
            'parroquia' => 'El Recreo',
            'direccion' => 'Av. Casanova',
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@sunagro.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Operador Polar',
            'email' => 'operador@polar.com',
            'password' => bcrypt('password'),
            'role' => 'operador',
            'empresa_id' => $origen->id,
        ]);

        Rubro::create([
            'nombre' => 'Harina de Maíz Precocida',
            'codigo_arancelario' => '1102.20.00',
            'unidad_medida' => 'TN',
            'presentacion' => 'Bulto 20 Kg',
            'precio_base' => 800.00,
        ]);

        Rubro::create([
            'nombre' => 'Arroz Blanco',
            'codigo_arancelario' => '1006.30.90',
            'unidad_medida' => 'TN',
            'presentacion' => 'Saco 24 Kg',
            'precio_base' => 750.00,
        ]);

        Conductor::create([
            'nombre_completo' => 'Pedro Rodriguez',
            'cedula' => 'V-15000000',
            'telefono' => '0412-9998877',
        ]);

        Vehiculo::create([
            'tipo' => 'Gandola',
            'placa' => 'A12B34C',
            'estatus' => 'Operativo',
        ]);
    


        $this->call([
            MacanaoMatoGrosoSeeder::class,
            GuiasSicaMatogrosoSeeder::class,
        ]);
    }
}
