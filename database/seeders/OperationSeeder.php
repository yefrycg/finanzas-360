<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Database\Seeder;

class OperationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            return;
        }

        $accounts = Account::where('user_id', $user->id)->get();
        $incomeCategories = Category::where('user_id', $user->id)->where('type', 'income')->get();
        $expenseCategories = Category::where('user_id', $user->id)->where('type', 'expense')->get();

        if ($accounts->isEmpty() || $incomeCategories->isEmpty() || $expenseCategories->isEmpty()) {
            return;
        }

        $checkingAccount = $accounts->firstWhere('type', 'checking_account') ?? $accounts->first();
        $cashAccount = $accounts->firstWhere('type', 'cash') ?? $accounts->first();
        $savingsAccount = $accounts->firstWhere('type', 'savings_account') ?? $accounts->first();

        $salarioCategory = $incomeCategories->firstWhere('name', 'Salario');
        $freelanceCategory = $incomeCategories->firstWhere('name', 'Freelance');
        $alimentacionCategory = $expenseCategories->firstWhere('name', 'Alimentación');
        $transporteCategory = $expenseCategories->firstWhere('name', 'Transporte');
        $serviciosCategory = $expenseCategories->firstWhere('name', 'Servicios');
        $entretenimientoCategory = $expenseCategories->firstWhere('name', 'Entretenimiento');

        $operations = [
            [
                'amount' => 3500000,
                'type' => 'income',
                'date_time' => now()->subDays(2),
                'note' => 'Salario quincena',
                'category_id' => $salarioCategory?->id ?? $incomeCategories->first()->id,
                'account_id' => $checkingAccount->id,
            ],
            [
                'amount' => 850000,
                'type' => 'income',
                'date_time' => now()->subDays(5),
                'note' => 'Proyecto freelance diseño web',
                'category_id' => $freelanceCategory?->id ?? $incomeCategories->first()->id,
                'account_id' => $checkingAccount->id,
            ],
            [
                'amount' => 185000,
                'type' => 'expense',
                'date_time' => now()->subDays(1),
                'note' => 'Mercado Éxito',
                'category_id' => $alimentacionCategory?->id ?? $expenseCategories->first()->id,
                'account_id' => $checkingAccount->id,
            ],
            [
                'amount' => 65000,
                'type' => 'expense',
                'date_time' => now()->subDays(3),
                'note' => 'Combustible',
                'category_id' => $transporteCategory?->id ?? $expenseCategories->first()->id,
                'account_id' => $checkingAccount->id,
            ],
            [
                'amount' => 145000,
                'type' => 'expense',
                'date_time' => now()->subDays(4),
                'note' => 'Servicios públicos',
                'category_id' => $serviciosCategory?->id ?? $expenseCategories->first()->id,
                'account_id' => $checkingAccount->id,
            ],
            [
                'amount' => 45000,
                'type' => 'expense',
                'date_time' => now()->subDays(6),
                'note' => 'Netflix + Spotify',
                'category_id' => $entretenimientoCategory?->id ?? $expenseCategories->first()->id,
                'account_id' => $checkingAccount->id,
            ],
            [
                'amount' => 1200000,
                'type' => 'income',
                'date_time' => now()->subDays(15),
                'note' => 'Ahorrotransferido a cuenta de ahorros',
                'category_id' => $salarioCategory?->id ?? $incomeCategories->first()->id,
                'account_id' => $checkingAccount->id,
            ],
            [
                'amount' => 75000,
                'type' => 'expense',
                'date_time' => now()->subDays(7),
                'note' => 'Cena Restaurant',
                'category_id' => $alimentacionCategory?->id ?? $expenseCategories->first()->id,
                'account_id' => $cashAccount->id,
            ],
        ];

        foreach ($operations as $operation) {
            Operation::create($operation + [
                'user_id' => $user->id,
            ]);
        }
    }
}
