<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Province::insert([
            ['province_name' => 'Aceh'],
            ['province_name' => 'Sumatera Utara'],
            ['province_name' => 'Sumatera Barat'],
            ['province_name' => 'Riau'],
            ['province_name' => 'Jambi'],
            ['province_name' => 'Sumatera Selatan'],
            ['province_name' => 'Bengkulu'],
            ['province_name' => 'Lampung'],
            ['province_name' => 'Kepulauan Bangka Belitung'],
            ['province_name' => 'Kepulauan Riau'],
            ['province_name' => 'DKI Jakarta'],
            ['province_name' => 'Jawa Barat'],
            ['province_name' => 'Jawa Tengah'],
            ['province_name' => 'DI Yogyakarta'],
            ['province_name' => 'Jawa Timur'],
            ['province_name' => 'Banten'],
            ['province_name' => 'Bali'],
            ['province_name' => 'Nusa Tenggara Barat'],
            ['province_name' => 'Nusa Tenggara Timur'],
            ['province_name' => 'Kalimantan Barat'],
            ['province_name' => 'Kalimantan Tengah'],
            ['province_name' => 'Kalimantan Selatan'],
            ['province_name' => 'Kalimantan Timur'],
            ['province_name' => 'Kalimantan Utara'],
            ['province_name' => 'Sulawesi Utara'],
            ['province_name' => 'Sulawesi Tengah'],
            ['province_name' => 'Sulawesi Selatan'],
            ['province_name' => 'Sulawesi Tenggara'],
            ['province_name' => 'Gorontalo'],
            ['province_name' => 'Sulawesi Barat'],
            ['province_name' => 'Maluku'],
            ['province_name' => 'Maluku Utara'],
            ['province_name' => 'Papua'],
            ['province_name' => 'Papua Barat'],
        ]);
    }
}
