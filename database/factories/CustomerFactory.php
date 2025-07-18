<?php

namespace Database\Factories;
use App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         $identityId = $this->faker->numberBetween(1, 3);

        return [
            'name'            => $this->faker->company(),
            'email'           => $this->faker->unique()->safeEmail(),
            'phone'           => $this->faker->phoneNumber(),
            'address'         => $this->faker->address(),
            'identity_id'     => $identityId,
            
            // 2. Si es “Sin documento” (1) → CF, si no → número aleatorio
            'document_number' => $identityId === 1
                ? 'CF'
                : $this->faker->unique()->numerify('##########'),

            'created_at'      => now(),
            'updated_at'      => now(),
        ];
    }
}
