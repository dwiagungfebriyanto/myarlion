<?php

namespace Database\Seeders;

use App\Models\SalesTargetMonthly;
use Illuminate\Database\Seeder;

class SalesTargetMonthlySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalesTargetMonthly::factory()->count(10)->create();
    }
}
