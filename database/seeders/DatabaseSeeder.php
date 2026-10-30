<?php

namespace Database\Seeders;

use App\Models\ContactSetting;
use App\Models\GalleryItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with initial baseline data.
     */
    public function run(): void
    {
        // 1. Seed Super Admin, Admin, and Content Manager Users
        User::firstOrCreate(
            ['email' => 'admin@comfortaircon.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('Admin@12345'),
                'role' => 'super_admin',
                'permissions' => ['products', 'gallery', 'enquiries', 'contact', 'users'],
                'status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@comfortaircon.com'],
            [
                'name' => 'Operations Manager',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
                'permissions' => ['products', 'gallery', 'enquiries', 'contact'],
                'status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'content@comfortaircon.com'],
            [
                'name' => 'Content Specialist',
                'password' => Hash::make('Admin@12345'),
                'role' => 'content_manager',
                'permissions' => ['products', 'gallery', 'contact'],
                'status' => 'active',
            ]
        );
    }
}
