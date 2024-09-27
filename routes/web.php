<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Artisan;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/refresh', function () {
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder']);
    return redirect()->back();
})->middleware(['auth', 'verified'])->name('refresh');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Category
    Route::get('/category', [TransactionController::class, 'categoryStore'])->name('category.store');
    Route::delete('/category/{category}', [TransactionController::class, 'categoryDestroy'])->name('categories.destroy');
    Route::post('/categories', [TransactionController::class, 'categoryStore'])->name('categories.store');
    // Transaction
    Route::get('/transaction', [TransactionController::class, 'index'])->name('transactions.index'); 
    Route::post('/transaction', [TransactionController::class, 'store'])->name('transactions.store');
    Route::delete('/transaction/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    // Goal
    Route::get('/goal', [GoalController::class, 'index'])->name('goal.index'); 
    Route::post('/goal', [GoalController::class, 'store'])->name('goal.store');
    Route::delete('/goal/{goal}', [GoalController::class, 'destroy'])->name('goal.destroy');
    Route::get('/goal-transaction', [GoalController::class, 'goalTransactionStore'])->name('goal-transaction.store');
    Route::delete('/goal-transaction/{goalTransaction}', [GoalController::class, 'goalTransactionDestroy'])->name('goal-transaction.destroy');
    // Budget
    Route::get('/budget', [BudgetController::class, 'index'])->name('budgets.index'); 
    Route::post('/budget', [BudgetController::class, 'store'])->name('budgets.store');
    Route::delete('/budget/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');
    Route::post('/budget-transaction', [BudgetController::class, 'budgetTransactionStore'])->name('budget-transactions.store');
    Route::delete('/budget-transaction/{budgetTransaction}', [BudgetController::class, 'budgetTransactionDestroy'])->name('budget-transactions.destroy');
    // Account
    Route::get('/account', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::delete('/accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
});

require __DIR__.'/auth.php';
