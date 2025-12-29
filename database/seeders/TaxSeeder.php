<?php

namespace Database\Seeders;

use App\Models\Tax;
use App\Models\Team;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Tax::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $team = Team::first();
        $faker = Faker::create('ms_MY'); //  id_ID
        $taxes = [
            ['name' => 'GST', 'percentage' => 10.00],
            ['name' => 'VAT', 'percentage' => 5.00],
            ['name' => 'PST', 'percentage' => 7.00],
            ['name' => 'Service Tax', 'percentage' => 8.00],
            ['name' => 'Luxury Tax', 'percentage' => 15.00],
        ];

        foreach ($taxes as $tax) {
            Tax::create([
                'name' => $tax['name'],
                'percentage' => $tax['percentage'],
                'description' => $faker->sentence(),
                'is_compound' => $faker->boolean(20), // 20% chance of being a compound tax
                'team_id' => $team->id,
            ]);
        }
    }
}
