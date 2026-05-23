<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDebtPaymentRequest;
use App\Http\Requests\StoreDebtRequest;
use App\Http\Requests\UpdateDebtRequest;
use App\Models\Debt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DebtController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Debt::class, 'debt');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $status = $request->query('status');

        $debtsQuery = $request->user()->debts()
            ->when($search, fn($query) => $query->where('lender', 'like', "%{$search}%"))
            ->when($dateFrom, fn($query) => $query->whereDate('start_date', '>=', $dateFrom))
            ->when($dateTo, fn($query) => $query->whereDate('end_date', '<=', $dateTo))
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderBy('end_date');

        $debts = $debtsQuery->paginate(10)->withQueryString();
        $debtsCollection = $debtsQuery->get();

        $totalPending = $debtsCollection->sum(fn($debt) => $debt->total_amount - ($debt->paid_amount ?? 0));
        $totalPaid = $debtsCollection->sum(fn($debt) => $debt->paid_amount ?? 0);
        $totalCount = $debtsCollection->count();
        $paidCount = $debtsCollection->where('status', 'paid')->count();

        return view('dashboard.debts.index', compact('debts', 'totalPending', 'totalPaid', 'paidCount', 'totalCount'));
    }

    public function store(StoreDebtRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $totalAmount = (float) $validated['total_amount'];
        $paidAmount = (float) ($validated['paid_amount'] ?? 0);

        $status = $paidAmount >= $totalAmount ? 'paid' : 'no_paid';

        $request->user()->debts()->create([
            'lender' => $validated['lender'],
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $status,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Deuda creada correctamente',
            ], 201);
        }

        return redirect()->route('dashboard.debts.index')->with('success', 'Deuda creada correctamente');
    }

    public function update(UpdateDebtRequest $request, Debt $debt): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $totalAmount = (float) $validated['total_amount'];
        $paidAmount = (float) ($validated['paid_amount'] ?? $debt->paid_amount);

        $status = $paidAmount >= $totalAmount ? 'paid' : 'no_paid';

        $debt->update([
            'lender' => $validated['lender'],
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $status,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Deuda actualizada correctamente',
            ]);
        }

        return redirect()->route('dashboard.debts.index')->with('success', 'Deuda actualizada correctamente');
    }

    public function addPayment(StoreDebtPaymentRequest $request, Debt $debt): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $paymentAmount = (float) $validated['payment_amount'];

        $newAmount = ($debt->paid_amount ?? 0) + $paymentAmount;
        $newStatus = $newAmount >= $debt->total_amount ? 'paid' : 'no_paid';

        DB::transaction(function () use ($debt, $newAmount, $newStatus) {
            $debt->paid_amount = $newAmount;
            $debt->status = $newStatus;
            $debt->save();
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pago añadido correctamente',
            ]);
        }

        return redirect()->route('dashboard.debts.index')->with('success', 'Pago añadido correctamente');
    }

    public function markAsPaid(Request $request, Debt $debt): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $debt);

        if ($debt->status === 'paid') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Esta deuda ya está pagada.',
                ], 409);
            }

            return redirect()->back()->with('error', 'Esta deuda ya está pagada.');
        }

        $debt->paid_amount = $debt->total_amount;
        $debt->status = 'paid';
        $debt->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Deuda marcada como pagada',
            ]);
        }

        return redirect()->route('dashboard.debts.index')->with('success', 'Deuda marcada como pagada');
    }

    public function destroy(Request $request, Debt $debt): RedirectResponse|JsonResponse
    {
        $debt->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Deuda eliminada correctamente',
            ]);
        }

        return redirect()->route('dashboard.debts.index')->with('success', 'Deuda eliminada correctamente');
    }
}
