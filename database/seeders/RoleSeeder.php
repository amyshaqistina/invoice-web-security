<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate Spatie permission tables
        Role::truncate();
        Permission::truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Permission::create(['name' => 'viewany']);

        $roleAdmin = Role::create(['name' => 'superadmin', 'guard_name' => 'web']);
        $roleAdmin->givePermissionTo(['viewany']);

        $roleAdmin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $roleAdmin->givePermissionTo(['viewany']);

        $roleAdmin = Role::create(['name' => 'customer', 'guard_name' => 'web']);
        $roleAdmin->givePermissionTo(['viewany']);

        $roleAdmin = Role::create(['name' => 'administrator', 'guard_name' => 'web']);
        $roleAdmin->givePermissionTo(['viewany']);

        $roleAdmin = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $roleAdmin->givePermissionTo(['viewany']);

        $roleAdmin = Role::create(['name' => 'accountant', 'guard_name' => 'web']);
        $roleAdmin->givePermissionTo(['viewany']);

        $roleAdmin = Role::create(['name' => 'sales', 'guard_name' => 'web']);
        $roleAdmin->givePermissionTo(['viewany']);





        // $roleAdmin = Role::create(['name' => 'ccd','guard_name' => 'web']);
        // $roleAdmin->givePermissionTo(['viewany']);

        // $roleAdmin = Role::create(['name' => 'ceo','guard_name' => 'web']);
        // $roleAdmin->givePermissionTo(['viewany']);

        // $roleExternal = Role::create(['name' => 'external','guard_name' => 'web']);
        // $roleExternal->givePermissionTo(['viewany']);




    }
}
