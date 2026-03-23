<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // --- 1. PRODUCTS WITH SACHET VARIATIONS ---
        $productGroups = [
            [1, 'UY-BW-BTL', 'Baby Wash (500ml)', 980.00, 'img/baby_wash.jpg', 'UY-S-WASH', 'img/Sachet_Wash.jpg'],
            [1, 'UY-HL-TBE', 'Hydrating Lotion (200ml)', 650.00, 'img/Lotion_Hydrating.jpg', 'UY-S-LOTN', 'img/Sachet_Lotion.jpg'],
            [1, 'UY-BC-JAR', 'Baby Bum Cream (100g)', 520.00, 'img/Face_Cream.jpg', 'UY-S-BUM', 'img/Sachet_BumCream.jpg'],
            [3, 'UY-SN-BTL', 'Body Sunscreen SPF50', 890.00, 'img/Body_Sunscreen.jpg', 'UY-S-SUN', 'img/Sachet_Sunscreen.jpg'],
            [2, 'UY-MO-BTL', 'Massage Oil (100ml)', 480.00, 'img/Liquid_Soap.jpg', 'UY-S-OIL', 'img/Sachet_MassageOil.jpg'],
        ];

        foreach ($productGroups as $group) {
            DB::table('products')->updateOrInsert([
                'sku' => $group[1],
            ], [
                'category_id' => $group[0],
                'sku'         => $group[1],
                'name'        => $group[2],
                'description' => "Premium gentle formula " . $group[2] . " for daily baby care.",
                'price'       => $group[3],
                'stock'       => 100,
                'visible'     => 1,
                'image'       => $group[4],
                'images'      => json_encode([$group[4]]),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            $parentId = DB::table('products')->where('sku', $group[1])->value('id');

            DB::table('products')->updateOrInsert([
                'sku' => $group[5],
            ], [
                'category_id' => 5, 
                'parent_id'   => $parentId,
                'sku'         => $group[5],
                'name'        => str_replace(['(500ml)', '(200ml)', '(100ml)', '(100g)'], '', $group[2]) . " (Sachet)",
                'description' => "Travel-friendly 3ml sachet. Perfect for testing on sensitive skin.",
                'price'       => 130.00, 
                'stock'       => 200,
                'visible'     => 1,
                'image'       => $group[6],
                'images'      => json_encode([$group[6]]),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        // --- 2. DIAPERS (6 Sizes x 3 Quantities) ---
        $diaperSizes = [
            ['size' => 'XS',  'img' => 'img/XS_Diaper.jpg'],
            ['size' => 'S',   'img' => 'img/Small_Diaper.jpg'],
            ['size' => 'M',   'img' => 'img/Medium_Diaper.jpg'],
            ['size' => 'L',   'img' => 'img/Large_Diaper.jpg'],
            ['size' => 'XL',  'img' => 'img/XL_Diaper.jpg'],
            ['size' => 'XXL', 'img' => 'img/XXL_Diaper.jpg'],
        ];
        $diaperQuantities = [
            ['pcs' => 12, 'price' => 350.00],
            ['pcs' => 24, 'price' => 670.00],
            ['pcs' => 36, 'price' => 990.00],
        ];
        foreach ($diaperSizes as $s) {
            $baseId = null;
            foreach ($diaperQuantities as $index => $q) {
                DB::table('products')->updateOrInsert([
                    'sku' => "UY-DP-{$s['size']}-{$q['pcs']}",
                ], [
                    'category_id' => 4,
                    'parent_id'   => ($index == 0) ? null : $baseId,
                    'sku'         => "UY-DP-{$s['size']}-{$q['pcs']}",
                    'name'        => "Ultra-Soft Diapers - {$s['size']} ({$q['pcs']} pcs)",
                    'description' => "Size {$s['size']} hypoallergenic diapers. Breathable and leak-proof.",
                    'price'       => $q['price'],
                    'stock'       => 150,
                    'visible'     => 1,
                    'image'       => $s['img'],
                    'images'      => json_encode([$s['img']]),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
                $id = DB::table('products')->where('sku', "UY-DP-{$s['size']}-{$q['pcs']}")->value('id');
                if ($index == 0) $baseId = $id;
            }
        }

        // --- 3. STANDALONE PRODUCTS ---
        $standalone = [
            [1, 'UY-BS-BAR', 'Bath Soap Bar', 180.00, 'img/Bath_Soap.jpg'],
            [2, 'UY-CR-JAR', 'Soothing Chest Rub', 320.00, 'img/Chest_Rub.jpg'],
            [2, 'UY-PJ-JAR', 'Pure Petroleum Jelly', 150.00, 'img/Petroleum_Jelly.jpg'],
            [2, 'UY-RS-SPR', 'Nappy Rash Spray', 450.00, 'img/Rash_Spray.jpg'],
            [2, 'UY-NB-TBE', 'Organic Nipple Balm', 580.00, 'img/Nipple_Balm.jpg'],
            [2, 'UY-SG-TBE', 'Aloe Soothing Gel', 290.00, 'img/Soothing_Gel.jpg'],
            [6, 'UY-CB-PK',  'Cotton Buds (200s)', 140.00, 'img/Cotton_Buds.jpg'],
            [6, 'UY-FC-BTL', 'Fabric Conditioner (1L)', 380.00, 'img/Fabric_Conditioner.jpg'],
            [4, 'UY-WP-PK',  'Baby Wipes (80 sheets)', 190.00, 'img/Wipes.jpg'],
            [3, 'UY-FS-SPF', 'Face Sunscreen SPF30', 620.00, 'img/Face_Sunscreen.jpg'],
        ];
        foreach ($standalone as $s) {
            DB::table('products')->updateOrInsert([
                'sku' => $s[1],
            ], [
                'category_id' => $s[0],
                'sku'         => $s[1],
                'name'        => $s[2],
                'description' => "High-quality " . $s[2] . " for delicate skin.",
                'price'       => $s[3],
                'stock'       => 120,
                'visible'     => 1,
                'image'       => $s[4],
                'images'      => json_encode([$s[4]]),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}
