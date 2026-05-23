<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            return;
        }

        $expenseCategories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->get();

        if ($expenseCategories->isEmpty()) {
            return;
        }

        $categoryIdByName = function (string $name) use ($expenseCategories): ?int {
            return $expenseCategories->firstWhere('name', $name)?->id;
        };

        $budgets = [
            [
                'name' => 'Gastos diarios (esenciales)',
                'period' => 'daily',
                'limit_amount' => 80000,
                'categories' => ['Alimentación', 'Transporte'],
            ],
            [
                'name' => 'Transporte semanal',
                'period' => 'weekly',
                'limit_amount' => 200000,
                'categories' => ['Transporte'],
            ],
            [
                'name' => 'Alimentación semanal',
                'period' => 'weekly',
                'limit_amount' => 300000,
                'categories' => ['Alimentación', 'Restaurant'],
            ],
            [
                'name' => 'Servicios del hogar',
                'period' => 'monthly',
                'limit_amount' => 350000,
                'categories' => ['Servicios', 'Internet'],
            ],
            [
                'name' => 'Entretenimiento mensual',
                'period' => 'monthly',
                'limit_amount' => 150000,
                'categories' => ['Entretenimiento'],
            ],
        ];

        foreach ($budgets as $budgetData) {
            $categoryIds = collect($budgetData['categories'])
                ->map(fn (string $name) => $categoryIdByName($name))
                ->filter()
                ->values();

            if ($categoryIds->isEmpty()) {
                $categoryIds = $expenseCategories
                    ->random(min(2, $expenseCategories->count()))
                    ->pluck('id');
            }

            $budget = Budget::updateOrCreate(
                ['user_id' => $user->id, 'name' => $budgetData['name']],
                [
                    'user_id' => $user->id,
                    'name' => $budgetData['name'],
                    'period' => $budgetData['period'],
                    'limit_amount' => $budgetData['limit_amount'],
                ]
            );

            $budget->categories()->sync($categoryIds->all());
        }
    }
}
