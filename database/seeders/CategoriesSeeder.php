<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Bath Essentials',
                'description' => 'Baby bath products and essentials',
                'is_active' => true,
            ],
            [
                'name' => 'Diapering Care',
                'description' => 'Diapers, wipes, and diapering accessories',
                'is_active' => true,
            ],
            [
                'name' => 'Skin Care',
                'description' => 'Baby skincare products and lotions',
                'is_active' => true,
            ],
            [
                'name' => 'Health & Hygiene',
                'description' => 'Health and hygiene products for babies',
                'is_active' => true,
            ],
            [
                'name' => 'Sachet Variants',
                'description' => 'Travel-friendly sachet versions of core products',
                'is_active' => true,
            ],
            [
                'name' => 'Household Essentials',
                'description' => 'Household and baby care support essentials',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
