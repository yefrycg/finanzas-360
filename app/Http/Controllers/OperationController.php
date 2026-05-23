<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperationRequest;
use App\Http\Requests\UpdateOperationRequest;
use App\Models\Account;
use App\Models\Operation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OperationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Operation::class, 'operation');
    }

    public function index(): View
    {
        $search = request('search');
        $type = request('type');
        $accountId = request('account_id');
        $categoryId = request('category_id');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $operations = request()->user()->operations()
            ->with(['category', 'account'])
            ->when($search, fn ($query) => $query->where(function ($q) use ($search) {
                $q->where('note', 'like', "%{$search}%")
                    ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('account', fn ($aq) => $aq->where('name', 'like', "%{$search}%"));
            }))
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($accountId, fn ($query) => $query->where('account_id', $accountId))
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when($dateFrom && $dateTo, fn ($query) => $query->whereBetween('date_time', [$dateFrom, $dateTo]))
            ->orderByDesc('date_time')
            ->paginate(10)
            ->withQueryString();

        $accounts = request()->user()->accounts()->orderBy('name')->get();
        $categories = request()->user()->categories()->orderBy('name')->get();

        $totalIncome = request()->user()->operations()
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = request()->user()->operations()
            ->where('type', 'expense')
            ->sum('amount');

        $totalOperations = request()->user()->operations()->count();

        return view('dashboard.operations.index', compact('operations', 'accounts', 'categories', 'totalIncome', 'totalExpense', 'totalOperations'));
    }

    public function show(Operation $operation): JsonResponse
    {
        $operation->load(['category', 'account']);

        return response()->json([
            'id' => $operation->id,
            'amount' => $operation->amount,
            'type' => $operation->type,
            'note' => $operation->note,
            'category_id' => $operation->category_id,
            'account_id' => $operation->account_id,
            'date_time' => $operation->date_time?->toIso8601String(),
            'date_time_input' => $operation->date_time?->format('Y-m-d\\TH:i'),
            'category' => $operation->category ? [
                'id' => $operation->category->id,
                'name' => $operation->category->name,
                'type' => $operation->category->type,
                'color' => $operation->category->color,
                'icon' => $operation->category->icon,
            ] : null,
            'account' => $operation->account ? [
                'id' => $operation->account->id,
                'name' => $operation->account->name,
                'type' => $operation->account->type,
                'color' => $operation->account->color,
                'icon' => $operation->account->icon,
            ] : null,
        ]);
    }

    public function store(StoreOperationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $amount = (float) $validated['amount'];
        $type = $validated['type'];

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'El monto debe ser mayor a 0.')->withInput();
        }

        $account = Account::where('id', $validated['account_id'])
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $account) {
            return redirect()->back()->with('error', 'Cuenta no encontrada.')->withInput();
        }

        if ($type === 'expense' && $amount > (float) $account->current_balance) {
            return redirect()->back()->with('error', 'Saldo insuficiente en esta cuenta.')->withInput();
        }

        DB::transaction(function () use ($validated, $amount, $account, $type) {
            $operation = Operation::create([
                'amount' => $amount,
                'date_time' => $validated['date_time'],
                'type' => $type,
                'note' => $validated['note'] ?? null,
                'category_id' => $validated['category_id'],
                'account_id' => $validated['account_id'],
                'user_id' => auth()->id(),
            ]);

            $account->refresh();

            if ($type === 'income') {
                $newBalance = $account->current_balance + $amount;
            } else {
                $newBalance = $account->current_balance - $amount;
            }

            DB::table('accounts')
                ->where('id', $account->id)
                ->update(['current_balance' => $newBalance]);
        });

        return redirect()->route('dashboard.operations.index')->with('success', 'Operación creada correctamente');
    }

    public function update(UpdateOperationRequest $request, Operation $operation): RedirectResponse
    {
        $validated = $request->validated();
        $newAmount = (float) $validated['amount'];

        if ($newAmount <= 0) {
            return redirect()->back()->with('error', 'El monto debe ser mayor a 0.')->withInput();
        }

        $oldAmount = (float) $operation->amount;
        $oldType = $operation->type;
        $oldAccountId = $operation->account_id;
        $newType = $validated['type'];
        $newAccountId = $validated['account_id'];

        $newAccount = Account::where('id', $newAccountId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $newAccount) {
            return redirect()->back()->with('error', 'Cuenta no encontrada.')->withInput();
        }

        if ($oldAccountId === $newAccountId) {
            $effectiveBalance = (float) $newAccount->current_balance;

            if ($oldType === 'expense') {
                $effectiveBalance += $oldAmount;
            }

            if ($newType === 'expense' && $newAmount > $effectiveBalance) {
                return redirect()->back()->with('error', 'Saldo insuficiente en esta cuenta.')->withInput();
            }
        } else {
            $oldAccount = Account::where('id', $oldAccountId)
                ->where('user_id', $request->user()->id)
                ->first();

            if (! $oldAccount) {
                return redirect()->back()->with('error', 'Cuenta original no encontrada.')->withInput();
            }

            if ($oldType === 'income') {
                DB::table('accounts')
                    ->where('id', $oldAccountId)
                    ->update(['current_balance' => DB::raw('current_balance - '.$oldAmount)]);
            } else {
                DB::table('accounts')
                    ->where('id', $oldAccountId)
                    ->update(['current_balance' => DB::raw('current_balance + '.$oldAmount)]);
            }

            if ($newType === 'expense' && $newAmount > (float) $newAccount->current_balance) {
                if ($oldType === 'income') {
                    DB::table('accounts')
                        ->where('id', $oldAccountId)
                        ->update(['current_balance' => DB::raw('current_balance + '.$oldAmount)]);
                } else {
                    DB::table('accounts')
                        ->where('id', $oldAccountId)
                        ->update(['current_balance' => DB::raw('current_balance - '.$oldAmount)]);
                }

                return redirect()->back()->with('error', 'Saldo insuficiente en la nueva cuenta.')->withInput();
            }
        }

        DB::transaction(function () use ($operation, $validated, $oldAmount, $oldType, $oldAccountId, $newAmount, $newType, $newAccountId, $newAccount) {
            if ($oldAccountId === $newAccountId) {
                if ($oldType === 'income') {
                    $newBalance = $newAccount->current_balance - $oldAmount;
                } else {
                    $newBalance = $newAccount->current_balance + $oldAmount;
                }

                if ($newType === 'income') {
                    $newBalance += $newAmount;
                } else {
                    $newBalance -= $newAmount;
                }

                DB::table('accounts')
                    ->where('id', $newAccountId)
                    ->update(['current_balance' => $newBalance]);
            } else {
                if ($newType === 'income') {
                    DB::table('accounts')
                        ->where('id', $newAccountId)
                        ->update(['current_balance' => DB::raw('current_balance + '.$newAmount)]);
                } else {
                    DB::table('accounts')
                        ->where('id', $newAccountId)
                        ->update(['current_balance' => DB::raw('current_balance - '.$newAmount)]);
                }
            }

            $operation->update([
                'amount' => $newAmount,
                'date_time' => $validated['date_time'],
                'type' => $newType,
                'note' => $validated['note'] ?? null,
                'category_id' => $validated['category_id'],
                'account_id' => $newAccountId,
            ]);
        });

        return redirect()->route('dashboard.operations.index')->with('success', 'Operación actualizada correctamente');
    }

    public function destroy(Operation $operation): RedirectResponse
    {
        $account = $operation->account;
        $amount = (float) $operation->amount;

        if (! $account) {
            return redirect()->back()->with('error', 'Cuenta no encontrada.');
        }

        DB::transaction(function () use ($operation, $account, $amount) {
            if ($operation->type === 'income') {
                DB::table('accounts')
                    ->where('id', $account->id)
                    ->update(['current_balance' => DB::raw('current_balance - '.$amount)]);
            } else {
                DB::table('accounts')
                    ->where('id', $account->id)
                    ->update(['current_balance' => DB::raw('current_balance + '.$amount)]);
            }

            $operation->delete();
        });

        return redirect()->route('dashboard.operations.index')->with('success', 'Operación eliminada correctamente');
    }
}
