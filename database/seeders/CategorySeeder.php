<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'name' => 'Smartphone & Tablet',
                'slug' => 'smartphone-tablet',
            ],
            [
                'name' => 'Laptop & Komputer',
                'slug' => 'laptop-komputer',
            ],
            [
                'name' => 'Audio & Video',
                'slug' => 'audio-video',
            ],
            [
                'name' => 'Gaming & Console',
                'slug' => 'gaming-console',
            ],
            [
                'name' => 'Aksesoris Elektronik',
                'slug' => 'aksesoris-elektronik',
            ],
            [
                'name' => 'Smart Home & IoT',
                'slug' => 'smart-home-iot',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
