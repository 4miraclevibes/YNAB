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

        Goal::create([
            'user_id' => 1,
            'account_id' => $account->id,
            'name' => 'Vacation',
            'target_amount' => 10000000,
            'current_amount' => 0,
            'deadline' => '2025-01-01'
        ]);
    }
}
