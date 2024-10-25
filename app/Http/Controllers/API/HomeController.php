<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isSqlite = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite';

        $averagePerDay = Category::withAvg(['transactions' => function ($query) use ($user) {
                $query->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                      ->where('accounts.user_id', $user->id);
            }], 'amount')
            ->withCount(['transactions' => function ($query) use ($user) {
                $query->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                      ->where('accounts.user_id', $user->id);
            }])
            ->get()
            ->map(function ($category) {
                $category->avg_per_day = $category->transactions_count > 0
                    ? $category->transactions_avg_amount
                    : 0;
                return $category;
            });

        $averagePerMonth = Category::withAvg(['transactions' => function ($query) use ($user) {
                $query->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                      ->where('accounts.user_id', $user->id);
            }], 'amount')
            ->withCount(['transactions' => function ($query) use ($user, $isSqlite) {
                $dateFormat = $isSqlite 
                    ? "strftime('%Y-%m', transactions.transaction_date)" 
                    : "DATE_FORMAT(transactions.transaction_date, '%Y-%m')";
                $query->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                      ->where('accounts.user_id', $user->id)
                      ->select(DB::raw("COUNT(DISTINCT {$dateFormat})"));
            }])
            ->get()
            ->map(function ($category) {
                $category->avg_per_month = $category->transactions_count > 0
                    ? $category->transactions_avg_amount * 30
                    : 0;
                return $category;
            });

        $averagePerYear = Category::withAvg(['transactions' => function ($query) use ($user) {
                $query->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                      ->where('accounts.user_id', $user->id);
            }], 'amount')
            ->withCount(['transactions' => function ($query) use ($user, $isSqlite) {
                $yearFunction = $isSqlite 
                    ? "strftime('%Y', transactions.transaction_date)" 
                    : "YEAR(transactions.transaction_date)";
                $query->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                      ->where('accounts.user_id', $user->id)
                      ->select(DB::raw("COUNT(DISTINCT {$yearFunction})"));
            }])
            ->get()
            ->map(function ($category) {
                $category->avg_per_year = $category->transactions_count > 0
                    ? $category->transactions_avg_amount * 365
                    : 0;
                return $category;
            });

        $endDate = Carbon::now();
        $ranges = [
            '1D' => Carbon::now()->subDay(),
            '1M' => Carbon::now()->subMonth(),
            '3M' => Carbon::now()->subMonths(3),
            'YTD' => Carbon::now()->startOfYear(),
            '1Y' => Carbon::now()->subYear(),
            '3Y' => Carbon::now()->subYears(3),
            '5Y' => Carbon::now()->subYears(5),
            '10Y' => Carbon::now()->subYears(10),
            'All' => Transaction::join('accounts', 'transactions.account_id', '=', 'accounts.id')
                                ->where('accounts.user_id', $user->id)
                                ->min('transactions.transaction_date')
        ];

        $transactions = [];
        foreach ($ranges as $key => $startDate) {
            $transactions[$key] = Transaction::selectRaw('DATE(transactions.transaction_date) as date, SUM(transactions.amount) as total')
                ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                ->where('accounts.user_id', $user->id)
                ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        return response()->json([
            'data' => [
                'transactions' => $transactions,
                'averagePerDay' => $averagePerDay,
                'averagePerMonth' => $averagePerMonth,
                'averagePerYear' => $averagePerYear
            ],
            'message' => 'Data beranda berhasil diambil',
            'code' => 200
        ], 200);
    }
}
