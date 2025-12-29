<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        //
        $user = User::factory()->create([
            'id' => '10000',
            'name' => 'superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@test.com',
            'password' => 'superadmin1234',
        ]);
        $user->assignRole(Role::where('name', 'superadmin')->first());

        $user = User::create([
            'phone' => '01137436150',
            'name' => 'admin',
            'email' => 'admin@test.com',
            'password' => 'admin1234',
        ]);
        $team = Team::first();
        $team->members()->syncWithoutDetaching([$user->id]);
        $user->assignRole(Role::where('name', 'admin')->first());

         $user = User::create([
            'phone' => '0112343212',
            'name' => 'customer',
            'email' => 'customer@test.com',
            'password' => 'customer1234',
        ]);
        $team = Team::first();
        $team->members()->syncWithoutDetaching([$user->id]);
        $user->assignRole(Role::where('name', 'customer')->first());

        // $user = User::factory()->create([
        //     'id' => '10001',
        //     'name' => 'staff',
        //     'username' => 'staff',
        //     'email' => 'staff@test.com',
        //     'password' => Hash::make('U53r_4cc0un7'),
        // ]);
        // $user->assignRole(Role::where('name', 'staff')->first());
    }
}
