<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                "name" => "Makan",
                "slug" => "makan",
                "user_id" => 1,
                "description" => "Makanan",
            ],
            [
                "name" => "Minum",
                "slug" => "minum",
                "user_id" => 1,
                "description" => "Minuman",
            ],
            [
                "name" => "Transportasi",
                "slug" => "transportasi",
                "user_id" => 1,
                "description" => "Transport",
            ],
            [
                "name" => "Education",
                "slug" => "education",
                "user_id" => 1,
                "description" => "Per sekolahan",
            ],
            [
                "name" => "Charity",
                "slug" => "charity",
                "user_id" => 1,
                "description" => "Ngasi orang",
            ],
            [
                "name" => "Healthcare",
                "slug" => "healthcare",
                "user_id" => 1,
                "description" => "Biar sehat",
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
