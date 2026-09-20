<?php

namespace Database\Seeders;

use App\Models\Specification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Specification::insert([
            ['main_category_id' => '1', 'code' => '010', 'specification_name' => 'Fine Grade'],
            ['main_category_id' => '1', 'code' => '011', 'specification_name' => 'Coarse Grade'],

            ['main_category_id' => '2', 'code' => '020', 'specification_name' => 'Compressed'],
            ['main_category_id' => '2', 'code' => '021', 'specification_name' => 'Loose'],

            ['main_category_id' => '3', 'code' => '030', 'specification_name' => 'Moisture Low'],
            ['main_category_id' => '3', 'code' => '031', 'specification_name' => 'Moisture Medium'],

            // [
            //     'main_category_id' => '1', 'code'                 => '001',
            //     'specification_name' => 'Natural'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '002',
            //     'specification_name' => 'Kadar Air Max 10%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '003',
            //     'specification_name' => 'Kadar Air Max 20%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '004',
            //     'specification_name' => 'Kadar Air Max 30%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '005',
            //     'specification_name' => 'Kadar Air Max 40%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '006',
            //     'specification_name' => 'Kadar Air Max 50%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '007',
            //     'specification_name' => 'Kadar Air Max 60%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '008',
            //     'specification_name' => 'Kadar Air Max 70%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '009',
            //     'specification_name' => 'Kadar Air Max 90%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '010',
            //     'specification_name' => 'Latex 5%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '011',
            //     'specification_name' => 'Latex 10%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '012',
            //     'specification_name' => 'Latex 20%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '013',
            //     'specification_name' => 'Latex 30%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '014',
            //     'specification_name' => 'Latex 40%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '015',
            //     'specification_name' => 'Latex 50%'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '016',
            //     'specification_name' => 'Sudah Cleaning'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '017',
            //     'specification_name' => 'Low Grade'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '018',
            //     'specification_name' => 'Medium Grade'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '019',
            //     'specification_name' => 'Best Grade'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '020',
            //     'specification_name' => 'Premium Grade'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '021',
            //     'specification_name' => 'Fine Grade'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '022',
            //     'specification_name' => 'Extra Fine Grade'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '023',
            //     'specification_name' => 'Carbonizer'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '024',
            //     'specification_name' => 'Kupas'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '025',
            //     'specification_name' => 'Dengan Ijuk dan Alas'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '026',
            //     'specification_name' => 'Dengan Ijuk Tanpa Alas'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '027',
            //     'specification_name' => 'Tanpa Ijuk Dengan Alas'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '028',
            //     'specification_name' => 'Tanpa Ijuk Tanpa Alas'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '029',
            //     'specification_name' => 'Size M (5mm-10mm)'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '030',
            //     'specification_name' => 'Size L (>10mm)'
            // ],
            // [
            //     'main_category_id' => '1', 'code'                 => '999',
            //     'specification_name' => 'Custom'
            // ],
        ]);
    }
}
