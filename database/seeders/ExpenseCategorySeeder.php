<?php

namespace Database\Seeders;

use App\Models\Team;
use Faker\Factory as Faker;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ExpenseCategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('ms_MY'); //  id_ID
        $team = Team::first();
        $categories = [
            'Office Supplies',
            'Travel',
            'Meals & Entertainment',
            'Utilities',
            'Rent',
            'Salary',
            'Software Subscriptions',
            'Marketing',
            'Equipment',
            'Insurance'
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create([
                'name' => $category,
                'team_id' => $team->id,
            ]);
        }
    }
}
