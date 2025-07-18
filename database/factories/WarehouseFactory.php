<?php
// database/factories/WarehouseFactory.php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'name'      => $this->faker->unique()->company(),
            'location'  => $this->faker->city() . ', ' . $this->faker->country(),
            'created_at'=> now(),
            'updated_at'=> now(),
        ];
    }
}
