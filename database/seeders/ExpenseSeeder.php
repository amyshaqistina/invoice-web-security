<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Expense;
use Faker\Factory as Faker;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Expense::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ms_MY'); //  id_ID
        $categories = ExpenseCategory::pluck('id')->toArray(); // Get all category IDs
        $team = Team::first();
        foreach (range(1, 10) as $index) { // Generate 10 fake expenses
            Expense::create([
                'category_id' => $faker->randomElement($categories), // Random category
                'date' => $faker->date(),
                'amount' => $faker->randomFloat(2, 10, 5000), // Random amount between 10 and 5000
                'note' => $faker->optional()->sentence(),
                'receipt' => $faker->optional()->imageUrl(200, 200, 'business'), // Fake receipt URL
                'team_id' => $team->id,
            ]);
        }
    }
}
