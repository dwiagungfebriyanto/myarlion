<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('currencies')->insert([
            [
                'currency_code' => 'AUD',
                'currency_name' => 'Australian Dollar'
            ],
            [
                'currency_code' => 'EUR',
                'currency_name' => 'Euro'
            ],
            [
                'currency_code' => 'IDR',
                'currency_name' => 'Rupiah'
            ],
            [
                'currency_code' => 'USD',
                'currency_name' => 'US Dollar'
            ],
        ]);
    }
}
