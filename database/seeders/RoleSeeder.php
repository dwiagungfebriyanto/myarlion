<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'name'       => 'administrator',
                'guard_name' => 'web',
            ],
            [
                'name'       => 'accounting',
                'guard_name' => 'web',
            ],
            [
                'name'       => 'marketing',
                'guard_name' => 'web',
            ],
            [
                'name'       => 'quality_control',
                'guard_name' => 'web',
            ],
        ]);
    }
}
