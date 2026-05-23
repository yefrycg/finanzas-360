<?php

namespace Database\Seeders;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Database\Seeder;

class DebtSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            return;
        }

        $debts = [
            [
                'lender' => 'Banco BBVA',
                'total_amount' => 2500000,
                'paid_amount' => 850000,
                'start_date' => now()->subMonths(8),
                'end_date' => now()->addMonths(4),
                'status' => 'no_paid',
            ],
            [
                'lender' => 'Cuota Familiar',
                'total_amount' => 1200000,
                'paid_amount' => 1200000,
                'start_date' => now()->subMonths(12),
                'end_date' => now()->subMonths(3),
                'status' => 'paid',
            ],
            [
                'lender' => 'Préstamo Amigos',
                'total_amount' => 500000,
                'paid_amount' => 0,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(4),
                'status' => 'no_paid',
            ],
            [
                'lender' => 'Bancolombia',
                'total_amount' => 4000000,
                'paid_amount' => 1500000,
                'start_date' => now()->subMonths(10),
                'end_date' => now()->addMonths(14),
                'status' => 'no_paid',
            ],
        ];

        foreach ($debts as $debt) {
            Debt::updateOrCreate(
                ['user_id' => $user->id, 'lender' => $debt['lender']],
                $debt + ['user_id' => $user->id]
            );
        }
    }
}
