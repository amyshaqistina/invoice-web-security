<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Team;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Item::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ms_MY'); //  id_ID

        $team = Team::first();


        $ProductNameSeeder = [
            'EchoGlow Smart Speaker',
            'Zenith Ergonomic Chair',
            'AquaPure Water Filter',
            'LumiCharge Wireless Charger',
            'Evergreen Reusable Tote',
            'SwiftFlow Bluetooth Earbuds',
            'Chef\'s Kiss Knife Set',
            'CloudNine Memory Foam Pillow',
            'TerraBloom Organic Fertilizer',
            'PixelPerfect HD Monitor'
        ];

        $listShortDesc = [
            'Immersive sound with intelligent voice control.',
            'Designed for ultimate comfort and posture support.',
            'Provides crystal-clear, great-tasting filtered water.',
            'Fast and convenient charging for all your devices.',
            'Durable and stylish for eco-conscious shopping.',
            'Crisp audio and comfortable fit for on-the-go.',
            'Premium stainless steel for precision cutting.',
            'Experience unparalleled comfort for a restful sleep.',
            'Nourishes plants for vibrant, healthy growth.',
            'Stunning visuals and vibrant colors for work or play.'
        ];

        foreach (range(1, 10) as $index) { // Generate 10 customers

            Item::create([
                'name' => $faker->randomElement($ProductNameSeeder),
                'info' => $faker->sentence(),
                'short_description' => $faker->randomElement($listShortDesc),
                'price' => $faker->randomFloat(2, 10, 500), // Price between RM10 - RM500
                'cost_price' => $faker->randomFloat(2, 5, 400), // Cost price between RM5 - RM400
                'weight' => $faker->randomFloat(3, 0.1, 10), // Weight between 0.1kg - 10kg
                'order_limit' => $faker->numberBetween(1, 100),
                'current_stock_balance' => $faker->numberBetween(0, 500),
                'activate_ecommerce' => $faker->boolean(80), // 80% chance of being true
                'activate_stock_management' => $faker->boolean(70),
                'activate_product_variations' => $faker->boolean(60),
                'directory' => $faker->word(),
                'attachment' => json_encode([
                    'image' => $faker->imageUrl(640, 480, 'products', true),
                ]),
                'team_id' => $team->id,
            ]);
        }
    }
}
