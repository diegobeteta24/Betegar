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
            // Reasons for incoming movements
          
            ['name' => 'Devolución del cliente', 'type' => 1],
            ['name' => 'Ajuste de inventario', 'type' => 1],
           
            // Reasons for outgoing movements
            
            ['name' => 'Devolución de venta', 'type' => 2],
            ['name' => 'Ajuste de inventario', 'type' => 2],
            ['name' => 'Gasto interno', 'type' => 2],

            
        ];

        foreach ($reasons as $reason) {
            Reason::Create($reason);
        }
    }
}
