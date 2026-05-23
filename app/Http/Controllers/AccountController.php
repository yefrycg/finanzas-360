<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Models\Operation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Account::class, 'account');
    }

    public function index(): View
    {
        $search = request('search');
        $type = request('type');

        $accounts = request()->user()->accounts()
            ->when($search, fn($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($type, fn($query) => $query->where('type', $type))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $totalBalance = request()->user()->accounts()->sum('current_balance');

        return view('dashboard.accounts.index', compact('accounts', 'totalBalance'));
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['color'] = Account::getColorByType($validated['type']);
        $validated['icon'] = Account::getIconByType($validated['type']);

        $request->user()->accounts()->create($validated);

        return redirect()->route('dashboard.accounts.index')->with('success', 'Cuenta creada correctamente');
    }

    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $validated = $request->validated();
        $validated['color'] = Account::getColorByType($validated['type']);
        $validated['icon'] = Account::getIconByType($validated['type']);

        $account->update($validated);

        return redirect()->route('dashboard.accounts.index')->with('success', 'Cuenta actualizada correctamente');
    }

    public function destroy(Account $account): RedirectResponse
    {
        if (Operation::where('account_id', $account->id)->exists()) {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar la cuenta porque tiene operaciones registradas.');
        }

        $account->delete();

        return redirect()->route('dashboard.accounts.index')->with('success', 'Cuenta eliminada correctamente');
    }
}
