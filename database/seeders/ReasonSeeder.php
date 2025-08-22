<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reason;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            // Entradas (1)
            ['name' => 'Compra a proveedor', 'type' => 1],
            ['name' => 'Devolución de cliente', 'type' => 1],
            ['name' => 'Ajuste positivo de inventario', 'type' => 1],
            ['name' => 'Traspaso desde otro almacén', 'type' => 1],
            ['name' => 'Producción/ensamblaje', 'type' => 1],

            // Salidas (2)
            ['name' => 'Venta a cliente', 'type' => 2],
            ['name' => 'Devolución a proveedor', 'type' => 2],
            ['name' => 'Ajuste negativo de inventario', 'type' => 2],
            ['name' => 'Traspaso a otro almacén', 'type' => 2],
            ['name' => 'Consumo interno / merma', 'type' => 2],
        ];

        foreach ($reasons as $reason) {
            Reason::firstOrCreate(['name' => $reason['name'], 'type' => $reason['type']], $reason);
        }
    }
}
