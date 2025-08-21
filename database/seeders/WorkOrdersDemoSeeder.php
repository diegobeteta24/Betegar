<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\WorkOrder;

class WorkOrdersDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there is at least one technician user
        $technician = User::whereHas('roles', fn($q) => $q->where('name','technician'))->first();
        if (!$technician) {
            $technician = User::factory()->create([
                'name' => 'Técnico Demo',
                'email' => 'tecnico.demo@example.com',
                'password' => bcrypt('password'),
            ]);
            $technician->assignRole('technician');
        }

        // Use an existing customer or create one
        $customer = Customer::first() ?? Customer::factory()->create([
            'name' => 'Cliente Demo',
        ]);

        // Create a pending work order for the technician
        WorkOrder::firstOrCreate([
            'customer_id' => $customer->id,
            'user_id' => $technician->id,
            'address' => 'Ciudad de Guatemala',
            'objective' => 'Instalación de equipo demo',
            'status' => 'pending',
        ]);
    }
}
