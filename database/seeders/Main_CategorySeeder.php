<?php

namespace Database\Seeders;

use App\Models\Main_Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Main_CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('main_categories')->insert([
            ['code' => '1', 'main_category_name' => 'Serat Alami'],
            ['code' => '2', 'main_category_name' => 'Produk Olahan'],
            ['code' => '3', 'main_category_name' => 'Material Organik'],

            // [
            //     'code' => '1',
            //     'main_category_name' => 'Kelapa & Turunannya',
            // ],
            // [
            //     'code' => '2',
            //     'main_category_name' => 'Karet & Olahannya',
            // ],
            // [
            //     'code' => '3',
            //     'main_category_name' => 'Biomassa',
            // ],
            // [
            //     'code' => '4',
            //     'main_category_name' => 'Hasil Alam',
            // ],
            // [
            //     'code' => '5',
            //     'main_category_name' => 'Kebutuhan Pertanian & Hobi',
            // ],
            // [
            //     'code' => '6',
            //     'main_category_name' => 'Peternakan dan Pet Supplies',
            // ],
            // [
            //     'code' => '7',
            //     'main_category_name' => 'Mesin',
            // ],
            // [
            //     'code' => '8',
            //     'main_category_name' => '--No Data--',
            // ],
            // [
            //     'code' => '9',
            //     'main_category_name' => 'Others',
            // ],
        ]);
    }
}
