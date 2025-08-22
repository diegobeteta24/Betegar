<?php

namespace Database\Factories;

use App\Models\Reason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Reason>
 */
class ReasonFactory extends Factory
{
    protected $model = Reason::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'type' => $this->faker->randomElement([1, 2]), // 1=incoming, 2=outgoing
        ];
    }

    public function incoming(): self
    {
        return $this->state(fn() => ['type' => 1]);
    }

    public function outgoing(): self
    {
        return $this->state(fn() => ['type' => 2]);
    }
}
