<?php

namespace Database\Seeders;

use App\Models\Goal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;
class GoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $account = Account::where('user_id', 1)->where('account_name', 'Goal Account')->first();
        if (!$account) {
            $account = Account::create([
                'user_id' => 1,
                'account_name' => 'Goal Account',
                'account_type' => 'Goal',
                'balance' => 0
            ]);
        } else {
            $account->delete();
            $account = Account::create([
                'user_id' => 1,
                'account_name' => 'Goal Account',
                'account_type' => 'Goal',
                'balance' => 0
            ]);
        }

        $monthly_income = 10000000; // Contoh gaji Rp 10.000.000
        $target_amount = 60000000;  // Target Rp 60.000.000
        $monthly_savings = $monthly_income * 0.25; // 25% dari gaji
        $months_needed = ceil($target_amount / $monthly_savings);
        
        Goal::create([
            'user_id' => 1,
            'account_id' => $account->id,
            'name' => 'Vacation',
            'target_amount' => $target_amount,
            'current_amount' => 0,
            'monthly_income' => $monthly_income,
            'monthly_savings' => $monthly_savings,
            'deadline' => now()->addMonths($months_needed)
        ]);
    }
}
