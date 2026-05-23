<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(['general_account', 'cash', 'checking_account', 'credit_card', 'savings_account']);

        return [
            'name' => $this->faker->word(),
            'type' => $type,
            'current_balance' => $this->faker->numberBetween(0, 10000),
            'credit_limit' => $type === 'credit_card' ? $this->faker->numberBetween(1000, 50000) : null,
            'color' => Account::getColorByType($type),
            'icon' => Account::getIconByType($type),
        ];
    }

    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'cash',
            'color' => Account::getColorByType('cash'),
            'icon' => Account::getIconByType('cash'),
        ]);
    }

    public function checkingAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'checking_account',
            'color' => Account::getColorByType('checking_account'),
            'icon' => Account::getIconByType('checking_account'),
        ]);
    }

    public function creditCard(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'credit_card',
            'current_balance' => 0,
            'credit_limit' => $this->faker->numberBetween(1000, 50000),
            'color' => Account::getColorByType('credit_card'),
            'icon' => Account::getIconByType('credit_card'),
        ]);
    }

    public function savingsAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'savings_account',
            'color' => Account::getColorByType('savings_account'),
            'icon' => Account::getIconByType('savings_account'),
        ]);
    }
}
