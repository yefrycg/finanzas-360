<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use App\Models\Operation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Operation>
 */
class OperationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 10, 5000),
            'date_time' => $this->faker->dateTimeThisMonth(),
            'type' => $this->faker->randomElement(['income', 'expense']),
            'note' => $this->faker->optional()->sentence(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Operation $operation) {
            if (! $operation->user_id) {
                $operation->update(['user_id' => $operation->category->user_id]);
            }
        });
    }

    public function income(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'income']);
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => ['type' => 'expense']);
    }
}
