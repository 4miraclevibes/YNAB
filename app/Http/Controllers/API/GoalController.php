<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\GoalTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::with('goalTransactions')->where('user_id', Auth::id())->get();
        
        return response()->json([
            'data' => ['goals' => $goals],
            'message' => 'Goals retrieved successfully',
            'code' => 200
        ]);
    }

    public function detail(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $goalTransactions = $goal->goalTransactions;

        return response()->json([
            'data' => [
                'goal' => $goal,
                'goalTransactions' => $goalTransactions
            ],
            'message' => 'Goal detail retrieved successfully',
            'code' => 200
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'deadline' => 'required|date',
        ]);

        $validatedData['user_id'] = Auth::id();
        $validatedData['current_amount'] = 0;

        $goal = Goal::create($validatedData);

        return response()->json([
            'data' => ['goal' => $goal],
            'message' => 'Goal created successfully',
            'code' => 201
        ], 201);
    }

    public function destroy(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $goal->delete();
        
        return response()->json([
            'data' => null,
            'message' => 'Goal deleted successfully',
            'code' => 200
        ]);
    }

    public function goalTransactionStore(Request $request)
    {
        $validatedData = $request->validate([
            'goal_id' => 'required|exists:goals,id',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
        ]);

        $goal = Goal::findOrFail($validatedData['goal_id']);

        if ($goal->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        $goalTransaction = GoalTransaction::create($validatedData);

        // Update current_amount of the goal
        $goal->current_amount += $validatedData['amount'];
        $goal->save();

        return response()->json([
            'data' => ['goalTransaction' => $goalTransaction],
            'message' => 'Goal transaction created successfully',
            'code' => 201
        ], 201);
    }

    public function goalTransactionDestroy(GoalTransaction $goalTransaction)
    {
        if ($goalTransaction->goal->user_id !== Auth::id()) {
            return response()->json([
                'data' => null,
                'message' => 'Unauthorized',
                'code' => 403
            ], 403);
        }

        // Update current_amount of the goal
        $goalTransaction->goal->current_amount -= $goalTransaction->amount;
        $goalTransaction->goal->save();

        $goalTransaction->delete();
        
        return response()->json([
            'data' => null,
            'message' => 'Goal transaction deleted successfully',
            'code' => 200
        ]);
    }
}
