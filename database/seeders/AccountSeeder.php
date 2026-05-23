<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            return;
        }

        $accounts = [
            [
                'name' => 'Banco de Bogotá',
                'type' => 'checking_account',
                'current_balance' => 3500000,
                'color' => '#3b82f6',
                'icon' => 'fas fa-building-columns',
            ],
            [
                'name' => 'Colchón',
                'type' => 'cash',
                'current_balance' => 200000,
                'color' => '#22c55e',
                'icon' => 'fas fa-money-bill',
            ],
            [
                'name' => 'Banco de Occidente',
                'type' => 'savings_account',
                'current_balance' => 5800000,
                'color' => '#8b5cf6',
                'icon' => 'fas fa-piggy-bank',
            ],
            [
                'name' => 'Davivienda',
                'type' => 'credit_card',
                'current_balance' => 450000,
                'credit_limit' => 5000000,
                'color' => '#ef4444',
                'icon' => 'fas fa-credit-card',
            ],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['user_id' => $user->id, 'name' => $account['name']],
                $account + ['user_id' => $user->id]
            );
        }
    }
}
