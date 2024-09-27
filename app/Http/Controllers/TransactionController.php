<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Budget;
use App\Models\BudgetTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');

        $transactionsQuery = Transaction::with(['category', 'account'])
            ->whereHas('account', function ($query) {
                $query->where('user_id', Auth::user()->id);
            });

        if ($categoryId) {
            $transactionsQuery->where('category_id', $categoryId);
        }

        $transactions = $transactionsQuery->get();
        $accounts = Account::where('user_id', Auth::user()->id)->get();
        $categories = Category::where('user_id', Auth::user()->id)->get();

        return view('pages.transaction', compact('transactions', 'accounts', 'categories', 'categoryId'));
    }

    public function store(Request $request)
    {
        try {
            $onBudget = $request->on_budget;
            $request->validate([
                'account_id' => 'required|exists:accounts,id',
                'category_id' => 'required|exists:categories,id',
                'amount' => 'required|numeric',
                'transaction_date' => 'required|date',
                'type' => 'required|in:income,expense',
                'description' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $account = Account::findOrFail($request->account_id);
            if ($request->type == 'income') {
                $account->balance += $request->amount;
            } else {
                $account->balance -= $request->amount;
            }
            $account->save();

            $data = $request->all();
            $data['user_id'] = Auth::id();
            $transaction = Transaction::create($data);

            if ($onBudget && $request->type == 'expense') {
                $budget = Budget::where('category_id', $request->category_id)
                                ->where('due_date', '>=', $request->transaction_date)
                                ->first();

                if ($budget) {
                    if ($budget->amount >= $request->amount) {
                        $budget->amount -= $request->amount;
                        $budget->save();

                        BudgetTransaction::create([
                            'budget_id' => $budget->id,
                            'amount' => $request->amount,
                            'transaction_date' => $request->transaction_date,
                            'description' => $request->description
                        ]);
                    } else {
                        throw new \Exception('Jumlah transaksi melebihi anggaran');
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil dihapus');
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|url',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->name);

        $category = Category::create($data);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function categoryDestroy(Category $category)
    {
        $category->delete();
        return redirect()->route('transactions.index')->with('success', 'Kategori berhasil dihapus');
    }
}
