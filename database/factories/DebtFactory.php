<?php

namespace Database\Factories;

use App\Models\Debt;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebtFactory extends Factory
{
    protected $model = Debt::class;

    public function definition(): array
    {
        return [
            'lender' => $this->faker->company(),
            'total_amount' => $this->faker->randomFloat(2, 1000, 100000),
            'paid_amount' => 0,
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'paid_amount' => $attributes['total_amount'],
            'status' => 'paid',
        ]);
    }
}
