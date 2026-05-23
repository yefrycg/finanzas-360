<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            return;
        }

        $categories = [
            ['name' => 'Salario', 'type' => 'income', 'color' => '#22c55e', 'icon' => 'fas fa-briefcase'],
            ['name' => 'Freelance', 'type' => 'income', 'color' => '#3b82f6', 'icon' => 'fas fa-laptop'],
            ['name' => 'Inversiones', 'type' => 'income', 'color' => '#8b5cf6', 'icon' => 'fas fa-chart-line'],
            ['name' => 'Bonus', 'type' => 'income', 'color' => '#f59e0b', 'icon' => 'fas fa-gift'],
            ['name' => 'Transporte', 'type' => 'expense', 'color' => '#f97316', 'icon' => 'fas fa-bus'],
            ['name' => 'Alimentación', 'type' => 'expense', 'color' => '#ef4444', 'icon' => 'fas fa-utensils'],
            ['name' => 'Servicios', 'type' => 'expense', 'color' => '#14b8a6', 'icon' => 'fas fa-lightbulb'],
            ['name' => 'Salud', 'type' => 'expense', 'color' => '#ec4899', 'icon' => 'fas fa-heartbeat'],
            ['name' => 'Entretenimiento', 'type' => 'expense', 'color' => '#a855f7', 'icon' => 'fas fa-film'],
            ['name' => 'Compras', 'type' => 'expense', 'color' => '#6366f1', 'icon' => 'fas fa-shopping-bag'],
            ['name' => 'Educación', 'type' => 'expense', 'color' => '#0891b2', 'icon' => 'fas fa-graduation-cap'],
            ['name' => 'Restaurant', 'type' => 'expense', 'color' => '#dc2626', 'icon' => 'fas fa-utensils'],
            ['name' => 'Vivienda', 'type' => 'expense', 'color' => '#1e40af', 'icon' => 'fas fa-home'],
            ['name' => 'Internet', 'type' => 'expense', 'color' => '#7c3aed', 'icon' => 'fas fa-wifi'],
        ];

        foreach ($categories as $category) {
            $user->categories()->updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
