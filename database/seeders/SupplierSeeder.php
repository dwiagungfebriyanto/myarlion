<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::insert([
            ['main_category_id' => '1', 'code' => '001', 'supplier_name' => 'PT Serat Nusantara'],
            ['main_category_id' => '1', 'code' => '002', 'supplier_name' => 'CV Alam Fiber'],

            ['main_category_id' => '2', 'code' => '001', 'supplier_name' => 'PT Olahan Prima'],
            ['main_category_id' => '2', 'code' => '002', 'supplier_name' => 'CV Bio Industri'],

            ['main_category_id' => '3', 'code' => '001', 'supplier_name' => 'PT Media Tumbuh'],
            ['main_category_id' => '3', 'code' => '002', 'supplier_name' => 'CV Organik Jaya'],

            // [
            //     'main_category_id' => '1', 'code'   => '001',
            //     'supplier_name' => 'Asep'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '002',
            //     'supplier_name' => 'Cholis'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '003',
            //     'supplier_name' => 'Enduk'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '004',
            //     'supplier_name' => 'Joko'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '005',
            //     'supplier_name' => 'Tri'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '006',
            //     'supplier_name' => 'Lucky'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '007',
            //     'supplier_name' => 'Sumber Tani'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '008',
            //     'supplier_name' => 'H Mansur'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '009',
            //     'supplier_name' => 'Wahyu'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '010',
            //     'supplier_name' => 'Han'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '011',
            //     'supplier_name' => 'Agus TP'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '012',
            //     'supplier_name' => 'Bambang'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '013',
            //     'supplier_name' => 'Songgolangit'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '014',
            //     'supplier_name' => 'Yudi Hartanto'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '015',
            //     'supplier_name' => 'Danny Nutrisi Bumi Lestari'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '016',
            //     'supplier_name' => 'CV Sebutret Indonesia'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '017',
            //     'supplier_name' => 'Tetes Tebu utara Star Gym'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '018',
            //     'supplier_name' => 'PT Bersama Optimis Sukses'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '019',
            //     'supplier_name' => 'Coconut Center'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '020',
            //     'supplier_name' => 'dr. Han'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '021',
            //     'supplier_name' => 'GBL Rina'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '022',
            //     'supplier_name' => 'Yayat, Cimerak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '023',
            //     'supplier_name' => 'Sugi'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '024',
            //     'supplier_name' => 'Dessicated Coconut Sumatera'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '025',
            //     'supplier_name' => 'Charles'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '026',
            //     'supplier_name' => 'Kelapa Sawit'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '027',
            //     'supplier_name' => 'Fikri'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '028',
            //     'supplier_name' => 'Cikalong'
            // ],
            // [
            //     'main_category_id' => '1', 'code'   => '029',
            //     'supplier_name' => 'Wood Pellet'
            // ],
        ]);
    }
}
