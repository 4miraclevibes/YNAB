<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Budget;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $budgets = [
            [
                "user_id" => 1,
                "category_id" => 1,
                "name" => "Makan",
                "amount" => 1500000,
                "due_date" => "2024-10-31",
                "status" => "on_budget",
            ],
            [
                "user_id" => 1,
                "category_id" => 2,
                "name" => "Minum",
                "amount" => 1000000,
                "due_date" => "2024-10-31",
                "status" => "on_budget",
            ],
            [
                "user_id" => 1,
                "category_id" => 3,
                "name" => "Transportasi",
                "amount" => 1500000,
                "due_date" => "2024-10-31",
                "status" => "on_budget",
            ],
        ];

        foreach ($budgets as $budget) {
            Budget::create($budget);
        }
    }
}
