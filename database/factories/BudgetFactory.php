<?php

namespace Database\Factories;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'period' => $this->faker->randomElement(['daily', 'weekly', 'monthly', 'annually']),
            'limit_amount' => $this->faker->randomFloat(2, 100, 5000),
        ];
    }
}
