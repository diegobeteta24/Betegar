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

        User::factory()->create([
            'name' => 'Diego Beteta',
            'email' => 'diegobeteta@distribuidorajadi.com',
            'password' => bcrypt('Gama5649')

        ]);
    

        $this->call([
            CategorySeeder::class,
            IdentitySeeder::class,
            ReasonSeeder::class,
        ]);

     Customer::factory(50)->create();
     Supplier::factory(5)->create();
     Warehouse::factory(2)->create();
    Product::factory(100)->create();
        
    
    }
}
