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
                "image" => "https://magazine.foodpanda.my/wp-content/uploads/sites/12/2020/05/cropped-7-Popular-Nasi-Padang-Spots-in-Shah-Alam.jpg",
            ],
            [
                "name" => "Minum",
                "slug" => "minum",
                "user_id" => 1,
                "description" => "Minuman",
                "image" => "https://www.rumahmesin.com/wp-content/uploads/2016/09/20-jenis-kopi-yang-populer-di-indonesia-dan-di-dunia-yang-patut-anda-coba-10.jpg",
            ],
            [
                "name" => "Transportasi",
                "slug" => "transportasi",
                "user_id" => 1,
                "description" => "Transport",
                "image" => "https://markey.id/wp-content/uploads/2019/09/439-teknologi-transportasi-adalah.jpg",
            ],
            [
                "name" => "Education",
                "slug" => "education",
                "user_id" => 1,
                "description" => "Per sekolahan",
                "image" => "https://img.freepik.com/premium-photo/education-back-school-concept-tree-with-learn-subjects-icons-book-illustration_999327-7706.jpg",
            ],
            [
                "name" => "Charity",
                "slug" => "charity",
                "user_id" => 1,
                "description" => "Ngasi orang",
                "image" => "https://th.bing.com/th/id/OIP.IhfJIkNRJud2-_lMSfJvtwHaEv?rs=1&pid=ImgDetMain",
            ],
            [
                "name" => "Healthcare",
                "slug" => "healthcare",
                "user_id" => 1,
                "description" => "Biar sehat",
                "image" => "https://boomi.com/wp-content/uploads/IMAGE-HealthcareCollage-e1520383886275.jpg",
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
