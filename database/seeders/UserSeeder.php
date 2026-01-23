<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin E-Commerce',
            'email' => 'admin@echo-shop.test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User E-Commerce',
            'email' => 'user@echo-shop.test',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);
    }
}
