<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Support\Facades\Auth;
use App\Models\BudgetTransaction;
use App\Models\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $budgetId = $request->query('budget_id');

        $categories = Category::where('user_id', Auth::user()->id)->get();
        $budgets = Budget::with('user', 'category')->where('user_id', Auth::user()->id)->get();

        $budgetTransactionsQuery = BudgetTransaction::with('budget')
            ->whereIn('budget_id', $budgets->pluck('id'));

        if ($budgetId) {
            $budgetTransactionsQuery->where('budget_id', $budgetId);
        }

        $budgetTransactions = $budgetTransactionsQuery->get();

        return view('pages.budget', compact('budgets', 'categories', 'budgetTransactions', 'budgetId'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $data['status'] = 'on_budget';
        $budget = Budget::create($data);
        return redirect()->back()->with('success', 'Anggaran berhasil ditambahkan');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return redirect()->route('budgets.index')->with('success', 'Anggaran berhasil dihapus');
    }

    public function budgetTransactionStore(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $budgetTransaction = BudgetTransaction::create($data);
        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function budgetTransactionDestroy(BudgetTransaction $budgetTransaction)
    {
        $budget = Budget::findOrFail($budgetTransaction->budget_id);
        $budget->amount += $budgetTransaction->amount;
        $budget->save();
        $budgetTransaction->delete();
        return redirect()->route('budgets.index')->with('success', 'Transaksi berhasil dihapus');
    }
}
