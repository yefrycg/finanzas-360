<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('accounts', AccountController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('operations', OperationController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::resource('goals', GoalController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/goals/{goal}/payment', [GoalController::class, 'addPayment'])->name('goals.payment');
    Route::post('/goals/{goal}/complete', [GoalController::class, 'markAsCompleted'])->name('goals.complete');
    Route::resource('debts', DebtController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('/debts/{debt}/payment', [DebtController::class, 'addPayment'])->name('debts.payment');
    Route::post('/debts/{debt}/paid', [DebtController::class, 'markAsPaid'])->name('debts.paid');
    Route::resource('budgets', BudgetController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
