<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $isSqlite = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'sqlite';

        $averagePerDay = Category::withAvg('transactions', 'amount')
            ->withCount('transactions')
            ->get()
            ->map(function ($category) {
                $category->avg_per_day = $category->transactions_count > 0
                    ? $category->transactions_avg_amount
                    : 0;
                return $category;
            });

        $averagePerMonth = Category::withAvg('transactions', 'amount')
            ->withCount(['transactions' => function ($query) use ($isSqlite) {
                $dateFormat = $isSqlite 
                    ? "strftime('%Y-%m', transaction_date)" 
                    : "DATE_FORMAT(transaction_date, '%Y-%m')";
                $query->select(DB::raw("COUNT(DISTINCT {$dateFormat})"));
            }])
            ->get()
            ->map(function ($category) {
                $category->avg_per_month = $category->transactions_count > 0
                    ? $category->transactions_avg_amount * 30 // Asumsi 30 hari per bulan
                    : 0;
                return $category;
            });

        $averagePerYear = Category::withAvg('transactions', 'amount')
            ->withCount(['transactions' => function ($query) use ($isSqlite) {
                $yearFunction = $isSqlite 
                    ? "strftime('%Y', transaction_date)" 
                    : "YEAR(transaction_date)";
                $query->select(DB::raw("COUNT(DISTINCT {$yearFunction})"));
            }])
            ->get()
            ->map(function ($category) {
                $category->avg_per_year = $category->transactions_count > 0
                    ? $category->transactions_avg_amount * 365 // Asumsi 365 hari per tahun
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
            'All' => Transaction::min('transaction_date')
        ];

        $transactions = [];
        foreach ($ranges as $key => $startDate) {
            $transactions[$key] = Transaction::selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        return view('home', compact('transactions', 'averagePerDay', 'averagePerMonth', 'averagePerYear'));
    }
}
