<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::insert([
            ['main_category_id' => '1', 'code' => '0', 'brand_name' => 'FiberTech'],
            ['main_category_id' => '1', 'code' => '1', 'brand_name' => 'NaturalWeave'],

            ['main_category_id' => '2', 'code' => '0', 'brand_name' => 'BioForm'],
            ['main_category_id' => '2', 'code' => '1', 'brand_name' => 'EcoBlock'],

            ['main_category_id' => '3', 'code' => '0', 'brand_name' => 'GrowMix'],
            ['main_category_id' => '3', 'code' => '1', 'brand_name' => 'SoilPro'],

            // // kelapa & turunannya
            // [
            //     'main_category_id' => '1',
            //     'code' => '0',
            //     'brand_name' => 'No Brand',
            // ],
            // [
            //     'main_category_id' => '1',
            //     'code' => '1',
            //     'brand_name' => 'Indococo',
            // ],
            // [
            //     'main_category_id' => '1',
            //     'code' => '2',
            //     'brand_name' => 'Tanami',
            // ],
            // [
            //     'main_category_id' => '1',
            //     'code' => '3',
            //     'brand_name' => 'Paw Republic',
            // ],


            // // karet olahan
            // [
            //     'main_category_id' => '2',
            //     'code' => '0',
            //     'brand_name' => 'No Brand',
            // ],
            // [
            //     'main_category_id' => '2',
            //     'code' => '1',
            //     'brand_name' => 'Indococo',
            // ],
            // [
            //     'main_category_id' => '2',
            //     'code' => '2',
            //     'brand_name' => 'Tanami',
            // ],
            // [
            //     'main_category_id' => '2',
            //     'code' => '3',
            //     'brand_name' => 'Paw Republic',
            // ],

            // // biomassa
            // [
            //     'main_category_id' => '3',
            //     'code' => '0',
            //     'brand_name' => 'No Brand',
            // ],
            // [
            //     'main_category_id' => '3',
            //     'code' => '1',
            //     'brand_name' => 'Indococo',
            // ],
            // [
            //     'main_category_id' => '3',
            //     'code' => '2',
            //     'brand_name' => 'Tanami',
            // ],
            // [
            //     'main_category_id' => '3',
            //     'code' => '3',
            //     'brand_name' => 'Paw Republic',
            // ],


            // // Hasil Alam
            // [
            //     'main_category_id' => '4',
            //     'code' => '0',
            //     'brand_name' => 'No Brand',
            // ],
            // [
            //     'main_category_id' => '4',
            //     'code' => '1',
            //     'brand_name' => 'Indococo',
            // ],
            // [
            //     'main_category_id' => '4',
            //     'code' => '2',
            //     'brand_name' => 'Tanami',
            // ],
            // [
            //     'main_category_id' => '4',
            //     'code' => '3',
            //     'brand_name' => 'Paw Republic',
            // ],

            // // Kebutuhan Pertanian & Hobi
            // [
            //     'main_category_id' => '5',
            //     'code' => '0',
            //     'brand_name' => 'No Brand',
            // ],
            // [
            //     'main_category_id' => '5',
            //     'code' => '1',
            //     'brand_name' => 'Indococo',
            // ],
            // [
            //     'main_category_id' => '5',
            //     'code' => '2',
            //     'brand_name' => 'Tanami',
            // ],
            // [
            //     'main_category_id' => '5',
            //     'code' => '3',
            //     'brand_name' => 'Paw Republic',
            // ],

            // // Peternakan dan Pet Suplies
            // [
            //     'main_category_id' => '6',
            //     'code' => '0',
            //     'brand_name' => 'No Brand',
            // ],
            // [
            //     'main_category_id' => '6',
            //     'code' => '1',
            //     'brand_name' => 'Indococo',
            // ],
            // [
            //     'main_category_id' => '6',
            //     'code' => '2',
            //     'brand_name' => 'Tanami',
            // ],
            // [
            //     'main_category_id' => '6',
            //     'code' => '3',
            //     'brand_name' => 'Paw Republic',
            // ],

            // // mesin
            // [
            //     'main_category_id' => '7',
            //     'code' => '0',
            //     'brand_name' => 'No Brand',
            // ],
            // [
            //     'main_category_id' => '7',
            //     'code' => '1',
            //     'brand_name' => 'Indococo',
            // ],
            // [
            //     'main_category_id' => '7',
            //     'code' => '2',
            //     'brand_name' => 'Tanami',
            // ],
            // [
            //     'main_category_id' => '7',
            //     'code' => '3',
            //     'brand_name' => 'Paw Republic',
            // ],

            // // otherrrs
            // [
            //     'main_category_id' => '9',
            //     'code' => '0',
            //     'brand_name' => 'No Brand',
            // ],
            // [
            //     'main_category_id' => '9',
            //     'code' => '1',
            //     'brand_name' => 'Indococo',
            // ],
            // [
            //     'main_category_id' => '9',
            //     'code' => '2',
            //     'brand_name' => 'Tanami',
            // ],
            // [
            //     'main_category_id' => '9',
            //     'code' => '3',
            //     'brand_name' => 'Paw Republic',
            // ],
        ]);
    }
}
