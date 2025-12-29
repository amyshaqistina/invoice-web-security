<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Customer;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Customer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ms_MY');
        $team = Team::first();

        foreach (range(1, 10) as $index) {
            Customer::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'paid_to_date' => $faker->randomFloat(2, 0, 10000),
                'balance' => $faker->randomFloat(2, -1000, 5000),
                'last_login' => $faker->dateTimeBetween('-1 year', 'now'),
                'team_id' => $team?->id, // Null-safe operator in case no team exists
            ]);
        }
    }
}
