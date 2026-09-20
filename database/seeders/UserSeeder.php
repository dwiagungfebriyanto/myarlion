<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name'      => 'admin',
            'position'  => 'admin',
            'username'  => 'admin',
            'role_id'   => '1',
            'password'  => bcrypt('admin'),
        ]);

        $admin->assignRole('administrator');
        $admin->givePermissionTo(Permission::pluck('name'));

        $marketing = User::create([
            'name'      => 'marketing',
            'position'  => 'marketing',
            'username'  => 'marketing',
            'role_id'   => '3',
            'password'  => bcrypt('marketing'),
        ]);

        $marketing->assignRole('marketing');
    }
}
