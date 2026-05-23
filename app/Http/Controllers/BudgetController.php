<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Budget::class, 'budget');
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $search = $request->string('search')->toString();
        $categoryId = $request->string('category_id')->toString();
        $period = $request->string('period')->toString();
        $status = $request->string('status')->toString();

        $budgets = $user->budgets()
            ->with('categories')
            ->when($search !== '', fn($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($period !== '', fn($query) => $query->where('period', $period))
            ->when($categoryId !== '', fn($query) => $query->whereHas('categories', fn($subQuery) => $subQuery->where('categories.id', $categoryId)))
            ->orderBy('created_at', 'desc')
            ->get();

        $expenseCategories = $user->categories()
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        $budgets = $this->hydrateComputedBudgetFields($budgets, $user->id, now());

        if ($status !== '') {
            $budgets = $budgets
                ->filter(fn(Budget $budget) => $budget->getAttribute('status') === $status)
                ->values();
        }

        $totalBudgets = $budgets->count();
        $totalLimit = (float) $budgets->sum('limit_amount');
        $totalSpent = (float) $budgets->sum('spent_amount');
        $exceededCount = $budgets->where('status', 'exceeded')->count();
        $totalRemaining = $totalLimit - $totalSpent;

        $paginatedBudgets = $this->paginateCollection($budgets, $request, perPage: 10);

        return view('dashboard.budgets.index', [
            'budgets' => $paginatedBudgets,
            'totalBudgets' => $totalBudgets,
            'totalLimit' => $totalLimit,
            'totalSpent' => $totalSpent,
            'totalRemaining' => $totalRemaining,
            'exceededCount' => $exceededCount,
            'categories' => $expenseCategories,
        ]);
    }

    public function store(StoreBudgetRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $budget = $request->user()->budgets()->create([
            'name' => $validated['name'],
            'period' => $validated['period'],
            'limit_amount' => $validated['limit_amount'],
        ]);

        $budget->categories()->sync($validated['categories']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Presupuesto creado correctamente',
                'data' => [
                    'id' => $budget->id,
                ],
            ], 201);
        }

        return redirect()->route('dashboard.budgets.index')->with('success', 'Presupuesto creado correctamente');
    }

    public function update(UpdateBudgetRequest $request, Budget $budget): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $budget->update([
            'name' => $validated['name'],
            'period' => $validated['period'],
            'limit_amount' => $validated['limit_amount'],
        ]);

        $budget->categories()->sync($validated['categories']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Presupuesto actualizado correctamente',
                'data' => [
                    'id' => $budget->id,
                ],
            ]);
        }

        return redirect()->route('dashboard.budgets.index')->with('success', 'Presupuesto actualizado correctamente');
    }

    public function destroy(Request $request, Budget $budget): RedirectResponse|JsonResponse
    {
        $budget->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Presupuesto eliminado correctamente',
            ]);
        }

        return redirect()->route('dashboard.budgets.index')->with('success', 'Presupuesto eliminado correctamente');
    }

    /**
     * @param  Collection<int, Budget>  $budgets
     * @return Collection<int, Budget>
     */
    private function hydrateComputedBudgetFields(Collection $budgets, int $userId, Carbon $now): Collection
    {
        $spentByBudgetId = $this->calculateSpentByBudgetId($budgets, $userId, $now);

        return $budgets->each(function (Budget $budget) use ($spentByBudgetId) {
            $spent = (float) ($spentByBudgetId[$budget->id] ?? 0);
            $limit = (float) $budget->limit_amount;
            $remaining = $limit - $spent;

            $progress = 0.0;
            if ($limit > 0) {
                $progress = ($spent / $limit) * 100;
            } elseif ($spent > 0) {
                $progress = 100;
            }

            $budget->setAttribute('spent_amount', $spent);
            $budget->setAttribute('remaining_amount', $remaining);
            $budget->setAttribute('progress', $progress);
            $budget->setAttribute('status', $spent > $limit ? 'exceeded' : 'active');
        });
    }

    /**
     * @param  Collection<int, Budget>  $budgets
     * @return array<int, float>
     */
    private function calculateSpentByBudgetId(Collection $budgets, int $userId, Carbon $now): array
    {
        if ($budgets->isEmpty()) {
            return [];
        }

        $spentByBudgetId = [];

        foreach ($budgets->groupBy('period') as $period => $periodBudgets) {
            [$start, $end] = $this->periodDateRange((string) $period, $now);
            $budgetIds = $periodBudgets->pluck('id')->all();

            $rows = DB::table('budget_category')
                ->join('operations', 'operations.category_id', '=', 'budget_category.category_id')
                ->whereIn('budget_category.budget_id', $budgetIds)
                ->where('operations.user_id', $userId)
                ->where('operations.type', 'expense')
                ->whereBetween('operations.date_time', [$start, $end])
                ->groupBy('budget_category.budget_id')
                ->selectRaw('budget_category.budget_id as budget_id, SUM(operations.amount) as spent_amount')
                ->pluck('spent_amount', 'budget_id')
                ->all();

            foreach ($rows as $budgetId => $spentAmount) {
                $spentByBudgetId[(int) $budgetId] = (float) $spentAmount;
            }
        }

        return $spentByBudgetId;
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function periodDateRange(string $period, Carbon $now): array
    {
        $now = $now->copy();

        return match ($period) {
            'daily' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'weekly' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'monthly' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'annually' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    /**
     * @param  Collection<int, Budget>  $items
     */
    private function paginateCollection(Collection $items, Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pageItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $pageItems,
            $items->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
