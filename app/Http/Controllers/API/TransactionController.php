<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Budget;
use App\Models\BudgetTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['category', 'account'])
            ->whereHas('account', function ($query) {
                $query->where('user_id', Auth::id());
            })->get();

        $accounts = Account::where('user_id', Auth::id())->get();
        $categories = Category::where('user_id', Auth::id())->get();

        return response()->json([
            'data' => [
                'transactions' => $transactions,
                'accounts' => $accounts,
                'categories' => $categories,
            ],
            'message' => 'Transactions retrieved successfully',
            'code' => 200
        ]);
    }

    public function detail(Category $category = null)
    {
        $transactionsQuery = Transaction::with(['category', 'account'])
            ->whereHas('account', function ($query) {
                $query->where('user_id', Auth::id());
            });

        if ($category) {
            $transactionsQuery->where('category_id', $category->id);
        }

        $transactions = $transactionsQuery->get();

        return response()->json([
            'data' => [
                'transactions' => $transactions,
                'category' => $category,
            ],
            'message' => 'Transaction details retrieved successfully',
            'code' => 200
        ]);
    }

    public function store(Request $request)
    {
        try {
            $onBudget = $request->on_budget;
            $validatedData = $request->validate([
                'account_id' => 'required|exists:accounts,id',
                'category_id' => 'required|exists:categories,id',
                'amount' => 'required|numeric',
                'transaction_date' => 'required|date',
                'type' => 'required|in:income,expense',
                'description' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $account = Account::findOrFail($validatedData['account_id']);
            if ($account->user_id !== Auth::id()) {
                throw new \Exception('Unauthorized');
            }

            if ($validatedData['type'] == 'income') {
                $account->balance += $validatedData['amount'];
            } else {
                $account->balance -= $validatedData['amount'];
            }
            $account->save();

            $validatedData['user_id'] = Auth::id();
            $transaction = Transaction::create($validatedData);

            if ($onBudget && $validatedData['type'] == 'expense') {
                $budget = Budget::where('category_id', $validatedData['category_id'])
                                ->where('due_date', '>=', $validatedData['transaction_date'])
                                ->first();

                if ($budget) {
                    if ($budget->amount >= $validatedData['amount']) {
                        $budget->amount -= $validatedData['amount'];
                        $budget->save();

                        BudgetTransaction::create([
                            'budget_id' => $budget->id,
                            'amount' => $validatedData['amount'],
                            'transaction_date' => $validatedData['transaction_date'],
                            'description' => $validatedData['description']
                        ]);
                    } else {
                        throw new \Exception('Transaction amount exceeds budget');
                    }
                }
            }

            DB::commit();

            return response()->json([
                'data' => ['transaction' => $transaction],
                'message' => 'Transaction created successfully',
                'code' => 201
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'data' => null,
                'message' => 'Error: ' . $e->getMessage(),
                'code' => 400
            ], 400);
        }
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->account->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $transaction->delete();

        return response()->json([
            'data' => null,
            'message' => 'Transaction deleted successfully',
            'code' => 200
        ]);
    }

    public function categoryStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|url',
        ]);

        $validatedData['user_id'] = Auth::id();
        $validatedData['slug'] = Str::slug($validatedData['name']);

        $category = Category::create($validatedData);

        return response()->json([
            'data' => ['category' => $category],
            'message' => 'Category created successfully',
            'code' => 201
        ], 201);
    }

    public function categoryDestroy(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $category->delete();

        return response()->json([
            'data' => null,
            'message' => 'Category deleted successfully',
            'code' => 200
        ]);
    }
}
