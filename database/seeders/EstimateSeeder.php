<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Customer;
use App\Models\Estimate;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EstimateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Estimate::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ms_MY'); // Set Malay locale

        // Get all customer IDs
        $customerIds = Customer::pluck('id')->toArray();
        $team = Team::first();
        for ($i = 0; $i < 10; $i++) { // Create 10 fake estimates
            Estimate::create([
                'customer_id' => $faker->randomElement($customerIds),
                'date' => $faker->date(),
                'expiry_date' => $faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),
                'estimate_number' => 'EST-' . strtoupper($faker->unique()->bothify('??###')),
                'notes' => $faker->optional()->sentence(10),
                'team_id' => $team->id,
            ]);
        }
    }
}
