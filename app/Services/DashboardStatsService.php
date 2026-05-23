<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Goal;
use App\Models\Operation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    private int $userId;
    private string $filter;
    private array $dateRange;

    public function __construct(int $userId, string $filter = 'this_month')
    {
        $this->userId = $userId;
        $this->filter = $filter;
        $this->dateRange = $this->resolveDateRange($filter);
    }

    public function getGreeting(): string
    {
        $hour = now()->hour;
        if ($hour < 12) {
            return 'Buenos días';
        }
        if ($hour < 18) {
            return 'Buenas tardes';
        }

        return 'Buenas noches';
    }

    public function getFinancialMessage(): string
    {
        [$start, $end] = $this->dateRange;
        $previousStart = $start->copy()->subMonth()->startOfMonth();
        $previousEnd = $start->copy()->subDay();

        $currentIncome = $this->getFilteredOperations('income')->sum('amount');
        $currentExpenses = $this->getFilteredOperations('expense')->sum('amount');
        $currentNet = $currentIncome - $currentExpenses;

        $prevIncome = Operation::byUser($this->userId)
            ->where('type', 'income')
            ->whereBetween('date_time', [$previousStart, $end->copy()->subDay()])
            ->sum('amount');
        $prevExpenses = Operation::byUser($this->userId)
            ->where('type', 'expense')
            ->whereBetween('date_time', [$previousStart, $end->copy()->subDay()])
            ->sum('amount');
        $prevNet = $prevIncome - $prevExpenses;

        if ($currentNet > $prevNet && $prevNet != 0) {
            $improvement = $currentNet >= 0 ? 'mejorando' : 'recuperándose';
            return "Tus finanzas están {$improvement} este período.";
        }
        if ($currentNet < $prevNet && $currentNet > 0) {
            return 'Tu gasto es mayor que el período anterior.';
        }
        if ($currentNet < 0) {
            return 'Estás gastando más de lo que ganas.';
        }

        return 'Tus finanzas están estables este período.';
    }

    public function getSummaryCards(): array
    {
        $accounts = Account::byUser($this->userId)->get();
        $totalBalance = $accounts->sum('current_balance');

        [$start, $end] = $this->dateRange;
        $monthlyIncome = $this->getFilteredOperations('income')->sum('amount');
        $monthlyExpenses = $this->getFilteredOperations('expense')->sum('amount');
        $netSavings = $monthlyIncome - $monthlyExpenses;

        $activeBudgets = Budget::byUser($this->userId)->count();
        $activeGoals = Goal::byUser($this->userId)->where('status', 'pending')->count();
        $pendingDebts = Debt::byUser($this->userId)->where('status', 'no_paid')->count();
        $totalAccounts = $accounts->count();

        return [
            'total_balance' => ['value' => $totalBalance, 'label' => 'Balance Total', 'trend' => null, 'positive' => true],
            'monthly_income' => ['value' => $monthlyIncome, 'label' => 'Ingresos del Mes', 'trend' => null, 'positive' => true],
            'monthly_expenses' => ['value' => $monthlyExpenses, 'label' => 'Gastos del Mes', 'trend' => null, 'positive' => false],
            'net_savings' => ['value' => $netSavings, 'label' => 'Ahorro Neto', 'trend' => null, 'positive' => $netSavings >= 0],
            'active_budgets' => ['value' => $activeBudgets, 'label' => 'Presupuestos Activos', 'trend' => null, 'positive' => true],
            'active_goals' => ['value' => $activeGoals, 'label' => 'Metas Activas', 'trend' => null, 'positive' => true],
            'pending_debts' => ['value' => $pendingDebts, 'label' => 'Deudas Pendientes', 'trend' => null, 'positive' => false],
            'total_accounts' => ['value' => $totalAccounts, 'label' => 'Total de Cuentas', 'trend' => null, 'positive' => true],
        ];
    }

    public function getCashflowData(): array
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->startOfMonth()->subMonths($i);
            $monthEnd = $monthStart->copy()->endOfMonth();

            $income = Operation::byUser($this->userId)
                ->where('type', 'income')
                ->whereBetween('date_time', [$monthStart, $monthEnd])
                ->sum('amount');

            $expense = Operation::byUser($this->userId)
                ->where('type', 'expense')
                ->whereBetween('date_time', [$monthStart, $monthEnd])
                ->sum('amount');

            $months[] = ucfirst($monthStart->locale('es')->format('M'));
            $incomeData[] = round($income, 2);
            $expenseData[] = round($expense, 2);
        }

        return [
            'labels' => $months,
            'income' => $incomeData,
            'expenses' => $expenseData,
        ];
    }

    public function getExpensesByCategory(): array
    {
        [$start, $end] = $this->dateRange;

        $expenses = Operation::byUser($this->userId)
            ->where('type', 'expense')
            ->whereBetween('date_time', [$start, $end])
            ->with('category:id,name,color,icon')
            ->get()
            ->groupBy('category_id')
            ->map(function ($ops) {
                $cat = $ops->first()->category;
                return [
                    'name' => $cat->name,
                    'color' => $cat->color,
                    'icon' => $cat->icon,
                    'amount' => $ops->sum('amount'),
                ];
            })
            ->values()
            ->sortByDesc('amount')
            ->values();

        $total = $expenses->sum('amount');

        return [
            'categories' => $expenses->map(function ($item) use ($total) {
                $item['percentage'] = $total > 0 ? round(($item['amount'] / $total) * 100, 1) : 0;
                return $item;
            })->values(),
            'total' => $total,
        ];
    }

    public function getAccounts(): array
    {
        $accounts = Account::byUser($this->userId)
            ->orderBy('name')
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'type' => $account->type,
                    'type_label' => Account::getTypeLabel($account->type),
                    'current_balance' => (float) $account->current_balance,
                    'color' => $account->color,
                    'icon' => $account->icon,
                ];
            });

        $totalBalance = $accounts->sum('current_balance');

        return [
            'accounts' => $accounts->values(),
            'total_balance' => $totalBalance,
        ];
    }

    public function getBudgetsStatus(): \Illuminate\Support\Collection
    {
        $budgets = Budget::byUser($this->userId)
            ->with(['categories:id,name,color,icon'])
            ->get()
            ->map(function ($budget) {
                [$start, $end] = $budget->currentPeriodDateRange();

                $categoryIds = $budget->categories->pluck('id');
                $spentAmount = Operation::byUser($this->userId)
                    ->where('type', 'expense')
                    ->whereIn('category_id', $categoryIds)
                    ->whereBetween('date_time', [$start, $end])
                    ->sum('amount');

                $limitAmount = (float) $budget->limit_amount;
                $spent = (float) $spentAmount;
                $remaining = $limitAmount - $spent;
                $percentage = $limitAmount > 0 ? min(($spent / $limitAmount) * 100, 100) : 0;
                $isExceeded = $spent > $limitAmount;

                $periodLabel = match ($budget->period) {
                    'daily' => 'diario',
                    'weekly' => 'semanal',
                    'monthly' => 'mensual',
                    'annually' => 'anual',
                    default => $budget->period,
                };

                return [
                    'id' => $budget->id,
                    'name' => $budget->name,
                    'period' => $periodLabel,
                    'limit_amount' => $limitAmount,
                    'spent_amount' => $spent,
                    'remaining' => $remaining,
                    'percentage' => round($percentage, 1),
                    'is_exceeded' => $isExceeded,
                    'categories' => $budget->categories->map(fn($c) => [
                        'name' => $c->name,
                        'color' => $c->color,
                        'icon' => $c->icon,
                    ])->values(),
                ];
            });

        return $budgets->values();
    }

    public function getGoalsProgress(): \Illuminate\Support\Collection
    {
        return Goal::byUser($this->userId)
            ->where('status', 'pending')
            ->with('category:id,name,color,icon')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($goal) {
                $target = (float) $goal->target_amount;
                $current = (float) $goal->current_amount;
                $remaining = $target - $current;
                $percentage = $target > 0 ? round(($current / $target) * 100, 1) : 0;

                return [
                    'id' => $goal->id,
                    'name' => $goal->name,
                    'target_amount' => $target,
                    'current_amount' => $current,
                    'remaining_amount' => $remaining,
                    'progress' => $percentage,
                    'due_date' => $goal->due_date?->locale('es')->format('d M, Y'),
                    'category' => $goal->category ? [
                        'name' => $goal->category->name,
                        'color' => $goal->category->color,
                        'icon' => $goal->category->icon,
                    ] : null,
                ];
            })
            ->values();
    }

    public function getDebtsOverview(): array
    {
        $debts = Debt::byUser($this->userId)->get();

        $totalDebt = $debts->sum('total_amount');
        $paidAmount = $debts->sum('paid_amount');
        $remainingDebt = $totalDebt - $paidAmount;
        $paidDebts = $debts->where('status', 'paid')->count();
        $pendingDebts = $debts->where('status', 'no_paid')->count();

        $recentDebts = $debts->where('status', 'no_paid')
            ->sortByDesc('created_at')
            ->take(5)
            ->map(function ($debt) {
                return [
                    'id' => $debt->id,
                    'lender' => $debt->lender,
                    'total_amount' => (float) $debt->total_amount,
                    'paid_amount' => (float) $debt->paid_amount,
                    'remaining' => (float) $debt->total_amount - (float) $debt->paid_amount,
                    'progress' => (float) $debt->total_amount > 0
                        ? round(((float) $debt->paid_amount / (float) $debt->total_amount) * 100, 1)
                        : 0,
                ];
            })
            ->values();

        return [
            'total_debt' => $totalDebt,
            'paid_amount' => $paidAmount,
            'remaining_debt' => $remainingDebt,
            'paid_debts' => $paidDebts,
            'pending_debts' => $pendingDebts,
            'recent_debts' => $recentDebts,
        ];
    }

    public function getRecentOperations(int $limit = 8)
    {
        return Operation::byUser($this->userId)
            ->with(['category:id,name,color,icon', 'account:id,name,color,icon'])
            ->orderByDesc('date_time')
            ->take($limit)
            ->get()
            ->map(function ($op) {
                return [
                    'id' => $op->id,
                    'amount' => (float) $op->amount,
                    'type' => $op->type,
                    'note' => $op->note,
                    'date_time' => $op->date_time->locale('es')->format('d M, H:i'),
                    'category' => [
                        'name' => $op->category->name,
                        'color' => $op->category->color,
                        'icon' => $op->category->icon,
                    ],
                    'account' => [
                        'name' => $op->account->name,
                        'color' => $op->account->color,
                        'icon' => $op->account->icon,
                    ],
                ];
            })
            ->values();
    }

    public function getInsights()
    {
        $insights = [];

        [$start, $end] = $this->dateRange;

        $expensesByCategory = Operation::byUser($this->userId)
            ->where('type', 'expense')
            ->whereBetween('date_time', [$start, $end])
            ->with('category:id,name')
            ->get()
            ->groupBy('category_id')
            ->map(fn($ops) => ['name' => $ops->first()->category->name, 'amount' => $ops->sum('amount')])
            ->sortByDesc('amount');

        if ($expensesByCategory->isNotEmpty()) {
            $top = $expensesByCategory->first();
            $insights[] = [
                'type' => 'info',
                'icon' => 'fa-solid fa-circle-info',
                'message' => "Tu mayor gasto este período es {$top['name']} (\${$this->formatNumber($top['amount'])}).",
            ];
        }

        $exceededBudgets = $this->getBudgetsStatus()->filter(fn($b) => $b['is_exceeded']);

        if ($exceededBudgets->isNotEmpty()) {
            $count = $exceededBudgets->count();
            $insights[] = [
                'type' => 'warning',
                'icon' => 'fa-solid fa-triangle-exclamation',
                'message' => "Excediste {$count} presupuesto" . ($count > 1 ? 's' : '') . " este período.",
            ];
        }

        $previousStart = $start->copy()->subMonth()->startOfMonth();
        $previousEnd = $start->copy()->subDay();

        $currentExpenses = Operation::byUser($this->userId)
            ->where('type', 'expense')
            ->whereBetween('date_time', [$start, $end])
            ->sum('amount');
        $previousExpenses = Operation::byUser($this->userId)
            ->where('type', 'expense')
            ->whereBetween('date_time', [$previousStart, $previousEnd])
            ->sum('amount');

        if ($previousExpenses > 0) {
            $change = (($currentExpenses - $previousExpenses) / $previousExpenses) * 100;
            if ($change < -10) {
                $insights[] = [
                    'type' => 'success',
                    'icon' => 'fa-solid fa-chart-line',
                    'message' => "Gastaste " . abs(round($change, 1)) . "% menos que el mes pasado.",
                ];
            } elseif ($change > 10) {
                $insights[] = [
                    'type' => 'danger',
                    'icon' => 'fa-solid fa-arrow-trend-up',
                    'message' => "Tus gastos aumentaron " . abs(round($change, 1)) . "% respecto al mes pasado.",
                ];
            }
        }

        $currentIncome = Operation::byUser($this->userId)
            ->where('type', 'income')
            ->whereBetween('date_time', [$start, $end])
            ->sum('amount');
        $previousIncome = Operation::byUser($this->userId)
            ->where('type', 'income')
            ->whereBetween('date_time', [$previousStart, $previousEnd])
            ->sum('amount');

        if ($previousIncome > 0) {
            $change = (($currentIncome - $previousIncome) / $previousIncome) * 100;
            if ($change > 10) {
                $insights[] = [
                    'type' => 'success',
                    'icon' => 'fa-solid fa-arrow-trend-up',
                    'message' => "Tus ingresos aumentaron " . abs(round($change, 1)) . "% respecto al mes pasado.",
                ];
            }
        }

        $paidThisPeriod = Debt::byUser($this->userId)
            ->where('status', 'paid')
            ->whereBetween('updated_at', [$start, $end])
            ->count();

        if ($paidThisPeriod > 0) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'fa-solid fa-check-circle',
                'message' => "¡Pagaste {$paidThisPeriod} deuda" . ($paidThisPeriod > 1 ? 's' : '') . " este período!",
            ];
        }

        $recentOps = $this->getRecentOperations(20);
        if ($recentOps->count() >= 10) {
            $expenseOps = $recentOps->where('type', 'expense');
            $incomeOps = $recentOps->where('type', 'income');
            if ($incomeOps->count() > $expenseOps->count()) {
                $insights[] = [
                    'type' => 'info',
                    'icon' => 'fa-solid fa-scale-balanced',
                    'message' => "Tienes más operaciones de ingreso que de gasto. ¡Sigue así!",
                ];
            }
        }

        return array_slice($insights, 0, 4);
    }

    private function getFilteredOperations(string $type)
    {
        [$start, $end] = $this->dateRange;

        return Operation::byUser($this->userId)
            ->where('type', $type)
            ->whereBetween('date_time', [$start, $end]);
    }

    private function resolveDateRange(string $filter): array
    {
        $now = now();

        return match ($filter) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'this_week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'this_year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    private function formatNumber(float $num): string
    {
        return number_format($num, 2);
    }
}