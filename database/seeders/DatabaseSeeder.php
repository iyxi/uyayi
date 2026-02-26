<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@uyayi.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123')
            ]
        );

        // Create test customer
        User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('password123')
            ]
        );

        // Create additional test users
        User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Smith',
                'password' => Hash::make('password123')
            ]
        );
    }
}
