<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BankAccount::insert([
            [
                'bank_id'        => '1',
                'currency_code'  => 'IDR',
                'account_number' => '123456789012',
                'account_name'   => 'Febriyanto',
            ],
            [
                'bank_id'        => '1',
                'currency_code'  => 'IDR',
                'account_number' => '987654321098',
                'account_name'   => 'Rizky Pratama',
            ],
            [
                'bank_id'        => '1',
                'currency_code'  => 'IDR',
                'account_number' => '112233445566',
                'account_name'   => 'PT Sumber Alam Sejahtera',
            ],
            [
                'bank_id'        => '2',
                'currency_code'  => 'USD',
                'account_number' => '110987654321',
                'account_name'   => 'PT Global Trade Nusantara',
            ],
            [
                'bank_id'        => '2',
                'currency_code'  => 'IDR',
                'account_number' => '223344556677',
                'account_name'   => 'PT Mitra Niaga Abadi',
            ],
            [
                'bank_id'        => '1',
                'currency_code'  => 'IDR',
                'account_number' => '556677889900',
                'account_name'   => 'Andi Saputra',
            ],

            // [
            //     'bank_id'        => '1',
            //     'currency_code'  => 'IDR',
            //     'account_number' => '8467003032',
            //     'account_name'   => 'Fajar Stevano Artha',
            // ],
            // [
            //     'bank_id'        => '1',
            //     'currency_code'  => 'IDR',
            //     'account_number' => '6801141622',
            //     'account_name'   => 'Fajar Stevano Artha',
            // ],
            // [
            //     'bank_id'        => '1',
            //     'currency_code'  => 'IDR',
            //     'account_number' => '1349908989',
            //     'account_name'   => 'PT. Visi Arlion Internasional',
            // ],
            // [
            //     'bank_id'        => '2',
            //     'currency_code'  => 'USD',
            //     'account_number' => '8888169990',
            //     'account_name'   => 'PT. Visi Arlion Internasional',
            // ],
            // [
            //     'bank_id'        => '2',
            //     'currency_code'  => 'IDR',
            //     'account_number' => '6661683333',
            //     'account_name'   => 'PT. Visi Arlion Internasional',
            // ],
            // [
            //     'bank_id'        => '1',
            //     'currency_code'  => 'IDR',
            //     'account_number' => '3740493085',
            //     'account_name'   => 'Gaudensius Winsen Setiawan',
            // ],
        ]);
    }
}
