<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            return;
        }

        $category = Category::where('user_id', $user->id)->first();

        if (! $category) {
            return;
        }

        $goals = [
            [
                'name' => 'Fondo de Emergencia',
                'target_amount' => 3000000,
                'current_amount' => 1200000,
                'due_date' => now()->addMonths(6),
                'status' => 'pending',
                'category_id' => $category->id,
            ],
            [
                'name' => 'Vacaciones Colombia',
                'target_amount' => 2000000,
                'current_amount' => 850000,
                'due_date' => now()->addMonths(4),
                'status' => 'pending',
                'category_id' => $category->id,
            ],
            [
                'name' => 'Computador Nuevo',
                'target_amount' => 2500000,
                'current_amount' => 2500000,
                'due_date' => now()->addMonths(1),
                'status' => 'completed',
                'category_id' => $category->id,
            ],
            [
                'name' => 'Curso Inglés',
                'target_amount' => 800000,
                'current_amount' => 350000,
                'due_date' => now()->addMonths(3),
                'status' => 'pending',
                'category_id' => $category->id,
            ],
            [
                'name' => 'Moto',
                'target_amount' => 15000000,
                'current_amount' => 3200000,
                'due_date' => now()->addMonths(12),
                'status' => 'pending',
                'category_id' => $category->id,
            ],
        ];

        foreach ($goals as $goal) {
            Goal::updateOrCreate(
                ['user_id' => $user->id, 'name' => $goal['name']],
                $goal + ['user_id' => $user->id]
            );
        }
    }
}
