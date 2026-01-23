<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@echo-shop.com'],
            [
                'name' => 'Admin ECHO-SHOP',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        
        User::updateOrCreate(
            ['email' => 'user@echo-shop.com'],
            [
                'name' => 'User ECHO-SHOP',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );
    }
}