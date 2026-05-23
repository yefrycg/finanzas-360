<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Goal;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoalFactory extends Factory
{
    protected $model = Goal::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'target_amount' => $this->faker->randomFloat(2, 100, 10000),
            'current_amount' => 0,
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'category_id' => Category::factory(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_amount' => $attributes['target_amount'],
            'status' => 'completed',
        ]);
    }
}
