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
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@uyayi.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'role' => 'admin'
            ]
        );
        // Mark admin as verified
        if (!$adminUser->hasVerifiedEmail()) {
            $adminUser->markEmailAsVerified();
        }

        // Create test customer
        $customerUser = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Test Customer',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'role' => 'customer'
            ]
        );
        // Mark test customer as verified for testing
        if (!$customerUser->hasVerifiedEmail()) {
            $customerUser->markEmailAsVerified();
        }

        // Create additional test users
        $johnUser = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Smith',
                'password' => Hash::make('password123'),
                'status' => 'active',
                'role' => 'customer'
            ]
        );
        // Mark john as verified for testing
        if (!$johnUser->hasVerifiedEmail()) {
            $johnUser->markEmailAsVerified();
        }

        // Seed categories
        $this->call([
            CategoriesSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
