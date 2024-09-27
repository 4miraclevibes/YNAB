<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Goal;
use App\Models\GoalTransaction;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::with('user')->where('user_id', Auth::user()->id)->get();
        return view('pages.goal', compact('goals'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $goal = Goal::create($data);
        return $goal;
    }

    public function destroy(Goal $goal)
    {
        $goal->delete();
        return $goal;
    }

    public function goalTransactionStore(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $goalTransaction = GoalTransaction::create($data);
        return $goalTransaction;
    }

    public function goalTransactionDestroy(GoalTransaction $goalTransaction)
    {
        $goalTransaction->delete();
        return $goalTransaction;
    }
}
