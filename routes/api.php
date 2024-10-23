<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\BudgetController;
use App\Http\Controllers\API\GoalController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;

// Public routes
Route::post('/login', [UserController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user', [UserController::class, 'show']);
    Route::get('/user/fetch', [UserController::class, 'fetch']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user/detail', [UserController::class, 'detail']);
    Route::put('/user/{id}', [UserController::class, 'update']);

    // Account routes
    Route::get('/accounts', [AccountController::class, 'index']);
    Route::get('/accounts/{account}', [AccountController::class, 'show']);
    Route::get('/accounts/{account}/detail', [AccountController::class, 'detail']);
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::put('/accounts/{account}', [AccountController::class, 'update']);
    Route::delete('/accounts/{account}', [AccountController::class, 'destroy']);

    // Budget routes
    Route::get('/budgets', [BudgetController::class, 'index']);
    Route::get('/budgets/{budget}', [BudgetController::class, 'detail']);
    Route::post('/budgets', [BudgetController::class, 'store']);
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy']);
    Route::post('/budget-transactions', [BudgetController::class, 'budgetTransactionStore']);
    Route::delete('/budget-transactions/{budgetTransaction}', [BudgetController::class, 'budgetTransactionDestroy']);

    // Goal routes
    Route::get('/goals', [GoalController::class, 'index']);
    Route::post('/goals', [GoalController::class, 'store']);
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy']);
    Route::post('/goal-transactions', [GoalController::class, 'goalTransactionStore']);
    Route::delete('/goal-transactions/{goalTransaction}', [GoalController::class, 'goalTransactionDestroy']);

    // Home route
    Route::get('/home', [HomeController::class, 'index']);

    // Transaction routes
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/detail/{category?}', [TransactionController::class, 'detail']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);
    Route::post('/categories', [TransactionController::class, 'categoryStore']);
    Route::delete('/categories/{category}', [TransactionController::class, 'categoryDestroy']);
});
