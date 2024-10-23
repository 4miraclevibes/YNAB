<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::where('user_id', Auth::id())->get();

        return response()->json([
            'data' => ['accounts' => $accounts],
            'message' => 'Accounts retrieved successfully',
            'code' => 200
        ], 200);
    }

    public function detail(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $transactions = Transaction::with(['account', 'category'])
            ->where('account_id', $account->id)
            ->get();

        return response()->json([
            'data' => [
                'account' => $account,
                'transactions' => $transactions
            ],
            'message' => 'Account details retrieved successfully',
            'code' => 200
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:cash,bank,credit_card,e_wallet',
            'balance' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        $account = Account::create($data);

        return response()->json([
            'data' => ['account' => $account],
            'message' => 'Account created successfully',
            'code' => 201
        ], 201);
    }

    public function destroy(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $account->delete();

        return response()->json([
            'data' => null,
            'message' => 'Account deleted successfully',
            'code' => 200
        ]);
    }

    public function update(Request $request, Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $request->validate([
            'account_name' => 'sometimes|required|string|max:255',
            'account_type' => 'sometimes|required|in:cash,bank,credit_card,e_wallet',
            'balance' => 'sometimes|required|numeric',
        ]);

        $account->update($request->all());

        return response()->json([
            'data' => ['account' => $account],
            'message' => 'Account updated successfully',
            'code' => 200
        ]);
    }

    public function show(Account $account)
    {
        if ($account->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        return response()->json([
            'data' => ['account' => $account],
            'message' => 'Account retrieved successfully',
            'code' => 200
        ], 200);
    }
}
