<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class AccountController extends Controller
{

    public function index(Request $request)
    {
        $accountId = $request->query('account_id');

        $accounts = Account::where('user_id', Auth::user()->id)->get();

        $transactionsQuery = Transaction::with(['account', 'category'])
            ->whereIn('account_id', $accounts->pluck('id'));

        if ($accountId) {
            $transactionsQuery->where('account_id', $accountId);
        }

        $transactions = $transactionsQuery->get();

        return view('pages.account', compact('accounts', 'transactions', 'accountId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:cash,bank,credit_card,e_wallet',
            'balance' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::user()->id;

        $account = Account::create($data);

        return redirect()->back()->with('success', 'Akun berhasil ditambahkan');    
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Akun berhasil dihapus');
    }
}
