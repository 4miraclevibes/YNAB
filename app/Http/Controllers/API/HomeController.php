<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $transactions = Transaction::selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->where('user_id', $user->id)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $averagePerDay = Category::withAvg(['transactions' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }], 'amount')
            ->withCount(['transactions' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get()
            ->map(function ($category) {
                $category->avg_per_day = $category->transactions_count > 0
                    ? $category->transactions_avg_amount
                    : 0;
                return $category;
            });

        $averagePerMonth = Category::withAvg(['transactions' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }], 'amount')
            ->withCount(['transactions' => function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->select(DB::raw('COUNT(DISTINCT DATE_FORMAT(transaction_date, "%Y-%m"))'));
            }])
            ->get()
            ->map(function ($category) {
                $category->avg_per_month = $category->transactions_count > 0
                    ? $category->transactions_avg_amount * 30 // Asumsi 30 hari per bulan
                    : 0;
                return $category;
            });

        $averagePerYear = Category::withAvg(['transactions' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }], 'amount')
            ->withCount(['transactions' => function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->select(DB::raw('COUNT(DISTINCT YEAR(transaction_date))'));
            }])
            ->get()
            ->map(function ($category) {
                $category->avg_per_year = $category->transactions_count > 0
                    ? $category->transactions_avg_amount * 365 // Asumsi 365 hari per tahun
                    : 0;
                return $category;
            });

        return response()->json([
            'data' => [
                'transactions' => $transactions,
                'averagePerDay' => $averagePerDay,
                'averagePerMonth' => $averagePerMonth,
                'averagePerYear' => $averagePerYear
            ],
            'message' => 'Home data retrieved successfully',
            'code' => 200
        ], 200);
    }
}