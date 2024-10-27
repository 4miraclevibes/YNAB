<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goal;
use App\Models\GoalTransaction;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::with('goalTransactions')->where('user_id', Auth::id())->get();
        return view('pages.goal', compact('goals'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'deadline' => 'required|date',
        ]);

        $account = Account::where('user_id', Auth::id())->where('account_name', 'Goal Account')->first();
        if (!$account) {
            return back()->with('error', 'Goal Account not found, make sure you have a "Goal Account" in your account list');
        }

        $validatedData['user_id'] = Auth::id();
        $validatedData['current_amount'] = 0;

        $goal = Goal::create($validatedData);

        return back()->with('success', 'Tujuan berhasil ditambahkan');
    }

    public function destroy(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $goal->delete();
        return back()->with('success', 'Tujuan berhasil dihapus');
    }

    public function goalTransactionStore(Request $request)
    {
        $validatedData = $request->validate([
            'goal_id' => 'required|exists:goals,id',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
        ]);

        $goal = Goal::findOrFail($validatedData['goal_id']);
        $account = Account::where('user_id', Auth::id())->where('account_name', 'Goal Account')->first();
        if (!$account) {
            return back()->with('error', 'Goal Account not found, make sure you have a "Goal Account" in your account list');
        }

        if ($goal->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        $goalTransaction = GoalTransaction::create($validatedData);

        // Update current_amount of the goal
        $goal->current_amount += $validatedData['amount'];
        $account->balance += $validatedData['amount'];
        $goal->save();
        $account->save();

        return back()->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function goalTransactionDestroy(GoalTransaction $goalTransaction)
    {
        $account = Account::where('user_id', Auth::id())->where('account_name', 'Goal Account')->first();
        if (!$account) {
            return back()->with('error', 'Goal Account not found, make sure you have a "Goal Account" in your account list');
        }

        if ($goalTransaction->goal->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        // Update current_amount of the goal
        $goalTransaction->goal->current_amount -= $goalTransaction->amount;
        $account->balance -= $goalTransaction->amount;
        $goalTransaction->goal->save();
        $account->save();

        $goalTransaction->delete();
        return back()->with('success', 'Transaksi berhasil dihapus');
    }
}
