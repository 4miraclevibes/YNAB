<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\BudgetTransaction;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        $budgets = Budget::with('user', 'category')->where('user_id', Auth::id())->get();

        return response()->json([
            'data' => [
                'categories' => $categories,
                'budgets' => $budgets,
            ],
            'message' => 'Budget data retrieved successfully',
            'code' => 200
        ]);
    }

    public function detail(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $budgetTransactions = BudgetTransaction::with('budget')
            ->where('budget_id', $budget->id)
            ->get();

        return response()->json([
            'data' => [
                'budget' => $budget,
                'budgetTransactions' => $budgetTransactions,
            ],
            'message' => 'Budget detail retrieved successfully',
            'code' => 200
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['status'] = 'on_budget';
        
        $budget = Budget::create($data);

        return response()->json([
            'data' => ['budget' => $budget],
            'message' => 'Budget created successfully',
            'code' => 201
        ], 201);
    }

    public function destroy(Budget $budget)
    {
        if ($budget->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $budget->delete();

        return response()->json([
            'data' => null,
            'message' => 'Budget deleted successfully',
            'code' => 200
        ]);
    }

    public function budgetTransactionStore(Request $request)
    {
        $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        $budget = Budget::findOrFail($data['budget_id']);
        if ($budget->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $budgetTransaction = BudgetTransaction::create($data);

        return response()->json([
            'data' => ['budgetTransaction' => $budgetTransaction],
            'message' => 'Budget transaction created successfully',
            'code' => 201
        ], 201);
    }

    public function budgetTransactionDestroy(BudgetTransaction $budgetTransaction)
    {
        if ($budgetTransaction->budget->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $budget = Budget::findOrFail($budgetTransaction->budget_id);
        $budget->amount += $budgetTransaction->amount;
        $budget->save();
        
        $budgetTransaction->delete();

        return response()->json([
            'data' => null,
            'message' => 'Budget transaction deleted successfully',
            'code' => 200
        ]);
    }
}
