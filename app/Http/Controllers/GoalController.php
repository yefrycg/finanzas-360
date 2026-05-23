<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoalPaymentRequest;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GoalController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Goal::class, 'goal');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $categoryId = $request->query('category_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $status = $request->query('status');

        $goalsQuery = $request->user()->goals()
            ->with('category')
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($dateFrom, fn ($query) => $query->whereDate('due_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('due_date', '<=', $dateTo))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('due_date');

        $goals = $goalsQuery->paginate(10)->withQueryString();
        $goalsCollection = $goalsQuery->get();

        $totalTarget = $goalsCollection->sum('target_amount');
        $totalSaved = $goalsCollection->sum(fn ($goal) => $goal->current_amount ?? 0);
        $totalRemaining = $totalTarget - $totalSaved;
        $totalCount = $goalsCollection->count();
        $completedCount = $goalsCollection->where('status', 'completed')->count();

        $categories = $request->user()->categories()->orderBy('name')->get();

        return view('dashboard.goals.index', compact('goals', 'totalTarget', 'totalSaved', 'totalRemaining', 'completedCount', 'totalCount', 'categories'));
    }

    public function store(StoreGoalRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $currentAmount = (float) ($validated['current_amount'] ?? 0);
        $targetAmount = (float) $validated['target_amount'];

        $status = $currentAmount >= $targetAmount ? 'completed' : 'pending';

        $goal = $request->user()->goals()->create([
            'name' => $validated['name'],
            'target_amount' => $targetAmount,
            'current_amount' => $currentAmount,
            'due_date' => $validated['due_date'],
            'category_id' => $validated['category_id'],
            'status' => $status,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Meta creada correctamente',
            ], 201);
        }

        return redirect()->route('dashboard.goals.index')->with('success', 'Meta creada correctamente');
    }

    public function update(UpdateGoalRequest $request, Goal $goal): RedirectResponse|JsonResponse
    {
        if ($goal->status === 'completed') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No puedes editar una meta completada.',
                ], 409);
            }

            return redirect()->back()->with('error', 'No puedes editar una meta completada.');
        }

        $validated = $request->validated();
        $currentAmount = (float) ($validated['current_amount'] ?? $goal->current_amount);
        $targetAmount = (float) $validated['target_amount'];

        $status = $currentAmount >= $targetAmount ? 'completed' : 'pending';

        $goal->update([
            'name' => $validated['name'],
            'target_amount' => $targetAmount,
            'current_amount' => $currentAmount,
            'due_date' => $validated['due_date'],
            'category_id' => $validated['category_id'],
            'status' => $status,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Meta actualizada correctamente',
            ]);
        }

        return redirect()->route('dashboard.goals.index')->with('success', 'Meta actualizada correctamente');
    }

    public function addPayment(StoreGoalPaymentRequest $request, Goal $goal): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $paymentAmount = (float) $validated['payment_amount'];

        $newAmount = ($goal->current_amount ?? 0) + $paymentAmount;
        $newStatus = $newAmount >= $goal->target_amount ? 'completed' : 'pending';

        DB::transaction(function () use ($goal, $newAmount, $newStatus) {
            $goal->current_amount = $newAmount;
            $goal->status = $newStatus;
            $goal->save();
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pago añadido correctamente',
            ]);
        }

        return redirect()->route('dashboard.goals.index')->with('success', 'Pago añadido correctamente');
    }

    public function markAsCompleted(Request $request, Goal $goal): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $goal);

        if ($goal->status === 'completed') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Esta meta ya está completada.',
                ], 409);
            }

            return redirect()->back()->with('error', 'Esta meta ya está completada.');
        }

        $goal->current_amount = $goal->target_amount;
        $goal->status = 'completed';
        $goal->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Meta marcada como completada',
            ]);
        }

        return redirect()->route('dashboard.goals.index')->with('success', 'Meta marcada como completada');
    }

    public function destroy(Request $request, Goal $goal): RedirectResponse|JsonResponse
    {
        $goal->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Meta eliminada correctamente',
            ]);
        }

        return redirect()->route('dashboard.goals.index')->with('success', 'Meta eliminada correctamente');
    }
}
