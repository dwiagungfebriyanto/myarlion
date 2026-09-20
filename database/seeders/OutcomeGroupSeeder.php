<?php

namespace Database\Seeders;

use App\Models\OutcomeGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OutcomeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OutcomeGroup::insert([
            ['name' => 'SALES'],
            ['name' => 'BANK CHARGES'],
            ['name' => 'COMMUNICATIONS'],
            ['name' => 'TRANSPORTATION & ACCOMODATION'],
            ['name' => 'ASSET & SUPPLIES'],
            ['name' => 'ELECTRICITY'],
            ['name' => 'MAINTENANCE'],
            ['name' => 'OFFICE RENTAL'],
            ['name' => 'SAMPLE'],
            ['name' => 'MAN POWER COST'],
            ['name' => 'INTERNET MARKETING'],
            ['name' => 'MISC'],
        ]);
    }
}
