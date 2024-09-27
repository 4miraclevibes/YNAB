<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                "user_id" => 1,
                "account_name" => "Tabungan makan",
                "account_type" => "bank",
                "balance" => 2000000,
            ],
            [
                "user_id" => 1,
                "account_name" => "Tabungan minum",
                "account_type" => "bank",
                "balance" => 1000000,
            ],
            [
                "user_id" => 1,
                "account_name" => "Tabungan hobby",
                "account_type" => "bank",
                "balance" => 5000000,
            ],
            [
                "user_id" => 1,
                "account_name" => "Tabungan transport",
                "account_type" => "bank",
                "balance" => 5000000,
            ],
            [
                "user_id" => 1,
                "account_name" => "Charity",
                "account_type" => "cash",
                "balance" => 1000000,
            ],
            [
                "user_id" => 1,
                "account_name" => "Tabungan Healthcare",
                "account_type" => "bank",
                "balance" => 5000000,
            ],
            [
                "user_id" => 1,
                "account_name" => "Tabungan education",
                "account_type" => "bank",
                "balance" => 10000000,
            ],
        ];

        foreach ($accounts as $account) {
            Account::create($account);
        }
    }
}
