<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Smartphone & Tablet
            [
                'name' => 'iPhone 15 Pro Max',
                'price' => 22999000,
                'stock' => 25,
                'image' => 'https://via.placeholder.com/300x300/007bff/ffffff?text=iPhone+15+Pro+Max',
                'description' => 'iPhone 15 Pro Max dengan chip A17 Pro, kamera 48MP, dan layar Super Retina XDR 6.7 inch.',
                'category_slug' => 'smartphone-tablet',
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'price' => 20999000,
                'stock' => 30,
                'image' => 'https://via.placeholder.com/300x300/28a745/ffffff?text=Galaxy+S24+Ultra',
                'description' => 'Samsung Galaxy S24 Ultra dengan S Pen, kamera 200MP, dan layar Dynamic AMOLED 6.8 inch.',
                'category_slug' => 'smartphone-tablet',
            ],
            [
                'name' => 'iPad Air 11 inch',
                'price' => 9999000,
                'stock' => 15,
                'image' => 'https://via.placeholder.com/300x300/6f42c1/ffffff?text=iPad+Air',
                'description' => 'iPad Air dengan chip M2, layar Liquid Retina 11 inch, dan support Apple Pencil.',
                'category_slug' => 'smartphone-tablet',
            ],

            // Laptop & Komputer
            [
                'name' => 'MacBook Air M3 13 inch',
                'price' => 18999000,
                'stock' => 10,
                'image' => 'https://via.placeholder.com/300x300/dc3545/ffffff?text=MacBook+Air',
                'description' => 'MacBook Air dengan chip M3, RAM 8GB, SSD 256GB, dan layar Liquid Retina 13.6 inch.',
                'category_slug' => 'laptop-komputer',
            ],
            [
                'name' => 'ASUS ROG Zephyrus G16',
                'price' => 24999000,
                'stock' => 8,
                'image' => 'https://via.placeholder.com/300x300/ffc107/000000?text=ROG+Zephyrus',
                'description' => 'ASUS ROG Zephyrus G16 dengan Intel Core i9, RTX 4070, RAM 32GB, dan layar 16 inch 165Hz.',
                'category_slug' => 'laptop-komputer',
            ],
            [
                'name' => 'iMac 24 inch M3',
                'price' => 15999000,
                'stock' => 5,
                'image' => 'https://via.placeholder.com/300x300/20c997/ffffff?text=iMac+M3',
                'description' => 'iMac dengan chip M3, RAM 8GB, SSD 256GB, dan layar Retina 4.5K 24 inch.',
                'category_slug' => 'laptop-komputer',
            ],

            // Audio & Video
            [
                'name' => 'AirPods Pro 2nd Gen',
                'price' => 3499000,
                'stock' => 50,
                'image' => 'https://via.placeholder.com/300x300/6c757d/ffffff?text=AirPods+Pro',
                'description' => 'AirPods Pro dengan chip H2, noise cancellation, dan spatial audio.',
                'category_slug' => 'audio-video',
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'price' => 4299000,
                'stock' => 25,
                'image' => 'https://via.placeholder.com/300x300/0d6efd/ffffff?text=Sony+XM5',
                'description' => 'Sony WH-1000XM5 dengan industry-leading noise cancellation dan 30-hour battery life.',
                'category_slug' => 'audio-video',
            ],
            [
                'name' => 'JBL Flip 6',
                'price' => 1299000,
                'stock' => 40,
                'image' => 'https://via.placeholder.com/300x300/6610f2/ffffff?text=JBL+Flip+6',
                'description' => 'JBL Flip 6 portable speaker dengan waterproof rating IP67 dan 12-hour battery life.',
                'category_slug' => 'audio-video',
            ],

            // Gaming & Console
            [
                'name' => 'PlayStation 5 Slim',
                'price' => 6999000,
                'stock' => 20,
                'image' => 'https://via.placeholder.com/300x300/003087/ffffff?text=PS5+Slim',
                'description' => 'PlayStation 5 Slim dengan 1TB SSD, ray tracing, dan 4K gaming.',
                'category_slug' => 'gaming-console',
            ],
            [
                'name' => 'Xbox Series X',
                'price' => 6499000,
                'stock' => 15,
                'image' => 'https://via.placeholder.com/300x300/107c10/ffffff?text=Xbox+Series+X',
                'description' => 'Xbox Series X dengan 1TB SSD, 4K gaming, dan backward compatibility.',
                'category_slug' => 'gaming-console',
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'price' => 4499000,
                'stock' => 25,
                'image' => 'https://via.placeholder.com/300x300/dc3545/ffffff?text=Switch+OLED',
                'description' => 'Nintendo Switch OLED dengan layar 7 inch OLED, 64GB storage, dan enhanced audio.',
                'category_slug' => 'gaming-console',
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::where('slug', $productData['category_slug'])->first();
            unset($productData['category_slug']);
            
            $productData['category_id'] = $category->id;
            Product::create($productData);
        }
    }
}
