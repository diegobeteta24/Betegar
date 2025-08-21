<?php

namespace Database\Factories;

use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    public function definition(): array
    {
        return [
            'name'            => 'Cuenta '.fake()->company(),
            'initial_balance' => fake()->numberBetween(500, 2000),
            'currency'        => 'GTQ',
            'description'     => fake()->sentence(),
        ];
    }
}
