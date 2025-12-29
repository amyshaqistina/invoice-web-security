<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Team::truncate();
        DB::table('team_user')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $team = Team::create([
            'name' => 'test',
            'slug' => 'test',
        ]);
    }
}
