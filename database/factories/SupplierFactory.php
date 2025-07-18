<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $identityId = $this->faker->numberBetween(1, 3);

        return [
            'identity_id'     => $identityId,
            'document_number' => $identityId === 1
                ? 'CF'
                : $this->faker->unique()->numerify('##########'),
            'name'            => $this->faker->company(),
            'address'         => $this->faker->address(),
            'email'           => $this->faker->unique()->safeEmail(),
            'phone'           => $this->faker->phoneNumber(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ];
    }
}
