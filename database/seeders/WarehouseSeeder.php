<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Warehouse::insert([
            [
                'warehouse_name' => 'Office 1',
                'address'        => 'Jalan Damai',
            ],
            [
                'warehouse_name' => 'Office 2',
                'address'        => 'Jalan Sejahtera',
            ],
        ]);
    }
}
