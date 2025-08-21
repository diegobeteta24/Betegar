<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Customer;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Warehouse;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

     

        // Solo ejecutar RoleSeeder para crear usuario admin (Diego) + roles/permisos
        $this->call([
            RoleSeeder::class,
            // CategorySeeder::class,
            // IdentitySeeder::class,
            // ReasonSeeder::class,
            // WorkOrdersDemoSeeder::class,
            // FullTinySeeder::class,
        ]);

    // (DemoFullSeeder deprecated by FullTinySeeder)
    }
}
