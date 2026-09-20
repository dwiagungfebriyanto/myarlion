<?php

namespace Database\Seeders;

use App\Models\Product_Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_types')->insert([
            ['main_category_id' => '1', 'code' => '100', 'product_type_name' => 'Lembaran'],
            ['main_category_id' => '1', 'code' => '101', 'product_type_name' => 'Gulungan'],

            ['main_category_id' => '2', 'code' => '200', 'product_type_name' => 'Padat Tekan'],
            ['main_category_id' => '2', 'code' => '201', 'product_type_name' => 'Granular'],

            ['main_category_id' => '3', 'code' => '300', 'product_type_name' => 'Campuran Kering'],
            ['main_category_id' => '3', 'code' => '301', 'product_type_name' => 'Siap Pakai'],

            // // Kelapa dan turunannya
            // [
            //     'main_category_id' => '1', 'code'            => '100',
            //     'product_type_name' => '100% Cocopeat High EC Tanpa Ayak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '101',
            //     'product_type_name' => '100% Cocopeat High EC Ayak 0,5'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '102',
            //     'product_type_name' => '100% Cocopeat High EC Ayak 0,3'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '103',
            //     'product_type_name' => '100% Cocopeat Low EC Tanpa Ayak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '104',
            //     'product_type_name' => '100% Cocopeat Low EC Ayak 0,5'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '105',
            //     'product_type_name' => '100% Cocopeat Low EC Ayak 0,3'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '106',
            //     'product_type_name' => '70% Cocopeat  Low EC 30% Cocohusk Tanpa Ayak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '107',
            //     'product_type_name' => '70% Cocopeat  Low EC 30% Cocohusk Ayak 0,5'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '108',
            //     'product_type_name' => '70% Cocopeat  Low EC 30% Cocohusk Ayak 0,3'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '109',
            //     'product_type_name' => '60% Cocopeat  Low EC 40% Cocohusk Tanpa Ayak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '110',
            //     'product_type_name' => '60% Cocopeat  Low EC 40% Cocohusk Ayak 0,5'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '111',
            //     'product_type_name' => '60% Cocopeat  Low EC 40% Cocohusk Ayak 0,3'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '112',
            //     'product_type_name' => '50% Cocopeat  Low EC 50% Cocohusk Tanpa Ayak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '113',
            //     'product_type_name' => '50% Cocopeat  Low EC 50% Cocohusk Ayak 0,5'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '114',
            //     'product_type_name' => '50% Cocopeat  Low EC 50% Cocohusk Ayak 0,3'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '115',
            //     'product_type_name' => '50% Cocopeat  Low EC 50% Cocofiber'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '116',
            //     'product_type_name' => '40% Cocopeat  Low EC 60% Cocohusk Tanpa Ayak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '117',
            //     'product_type_name' => '40% Cocopeat  Low EC 60% Cocohusk Ayak 0,5'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '118',
            //     'product_type_name' => '40% Cocopeat  Low EC 60% Cocohusk Ayak 0,3'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '119',
            //     'product_type_name' => '60% Cocopeat  Low EC 40% Perlite Tanpa Ayak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '120',
            //     'product_type_name' => '60% Cocopeat  Low EC 40% Perlite Ayak 0,5'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '121',
            //     'product_type_name' => '60% Cocopeat  Low EC 40% Perlite Ayak 0,3'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '122',
            //     'product_type_name' => '50% Cocopeat  Low EC 50% Perlite Tanpa Ayak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '123',
            //     'product_type_name' => '50% Cocopeat  Low EC 50% Perlite Ayak 0,5'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '124',
            //     'product_type_name' => '50% Cocopeat  Low EC 50% Perlite Ayak 0,3'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '125',
            //     'product_type_name' => '40% Cocopeat  Low EC 60% Perlite Tanpa Ayak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '126',
            //     'product_type_name' => '40% Cocopeat  Low EC 60% Perlite Ayak 0,5'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '127',
            //     'product_type_name' => '40% Cocopeat  Low EC 60% Perlite Ayak 0,3'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '128',
            //     'product_type_name' => 'Cocopeat Shredded'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '140',
            //     'product_type_name' => 'Cocofiber Ekspor'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '141',
            //     'product_type_name' => 'Cocofiber Lokal'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '142',
            //     'product_type_name' => 'Cocofiber Bale Ekspor'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '143',
            //     'product_type_name' => 'Cocofiber Bale Lokal'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '150',
            //     'product_type_name' => 'Cocohusk Low EC'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '151',
            //     'product_type_name' => 'Cocohusk High EC'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '160',
            //     'product_type_name' => 'Cocochips Low EC'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '161',
            //     'product_type_name' => 'Cocochips High EC'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '170',
            //     'product_type_name' => 'Cocosheet Tipis <1 cm'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '171',
            //     'product_type_name' => 'Cocosheet Tebal 1 cm'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '172',
            //     'product_type_name' => 'Cocosheet Tebal 2 cm'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '173',
            //     'product_type_name' => 'Cocosheet Tebal 3 cm'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '174',
            //     'product_type_name' => 'Cocosheet Tebal 5 cm'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '175',
            //     'product_type_name' => 'Cocosheet Tebal 8 cm'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '176',
            //     'product_type_name' => 'Cocosheet Tebal 10 cm'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '177',
            //     'product_type_name' => 'Cocosheet Tebal 30 cm'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '190',
            //     'product_type_name' => 'Coco Mulsa Bulat'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '191',
            //     'product_type_name' => 'Coco Mulsa Kotak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '200',
            //     'product_type_name' => 'Cocorope - Benang Nylon'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '201',
            //     'product_type_name' => 'Cocorope - Benang Cotton'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '210',
            //     'product_type_name' => 'Coco Bristle Warna Natural'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '211',
            //     'product_type_name' => 'Coco Bristle Warna Hitam'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '220',
            //     'product_type_name' => 'Coir Geotextile / Cocomesh'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '221',
            //     'product_type_name' => 'Coir Blanket isi Cocofiber'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '222',
            //     'product_type_name' => 'Coir Blanket isi Jerami'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '223',
            //     'product_type_name' => 'Coir Roll'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '224',
            //     'product_type_name' => 'Coir Mat'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '230',
            //     'product_type_name' => 'Cocopot Press Kotak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '231',
            //     'product_type_name' => 'Cocopot Press Persegi Panjang'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '232',
            //     'product_type_name' => 'Cocopot Press Bulat'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '233',
            //     'product_type_name' => 'Cocopot Press Setengah Lingkaran'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '234',
            //     'product_type_name' => 'Cocopot Press Hexagonal'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '235',
            //     'product_type_name' => 'Cocopot Press Basket'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '236',
            //     'product_type_name' => 'Cocopot Press Lainnya'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '250',
            //     'product_type_name' => 'Cocopot Anyam Kotak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '251',
            //     'product_type_name' => 'Cocopot Anyam Persegi Panjang'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '252',
            //     'product_type_name' => 'Cocopot Anyam Bulat'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '253',
            //     'product_type_name' => 'Cocopot Anyam Setengah Lingkaran'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '254',
            //     'product_type_name' => 'Cocopot Anyam Hexagonal'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '255',
            //     'product_type_name' => 'Cocopot Anyam Cone'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '256',
            //     'product_type_name' => 'Cocopot Anyam Basket'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '257',
            //     'product_type_name' => 'Cocopot Anyam Lainnya'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '270',
            //     'product_type_name' => 'Cocopot Wiremesh Kotak'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '271',
            //     'product_type_name' => 'Cocopot Wiremesh Persegi Panjang'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '272',
            //     'product_type_name' => 'Cocopot Wiremesh Bulat'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '273',
            //     'product_type_name' => 'Cocopot Wiremesh Setengah Lingkaran Dengan Sisi Gantung'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '274',
            //     'product_type_name' => 'Cocopot Wiremesh Setengah Lingkara Tanpa Sisi Gantung'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '275',
            //     'product_type_name' => 'Cocopot Wiremesh Hexagonal'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '276',
            //     'product_type_name' => 'Cocopot Wiremesh Hati'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '277',
            //     'product_type_name' => 'Cocopot Wiremesh Cone'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '278',
            //     'product_type_name' => 'Cocopot Wiremesh Basket'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '279',
            //     'product_type_name' => 'Cocopot Wiremesh Lainnya'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '290',
            //     'product_type_name' => 'Palmrope'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '291',
            //     'product_type_name' => 'Palmpeat'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '292',
            //     'product_type_name' => 'Palmmesh'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '293',
            //     'product_type_name' => 'Palmfiber'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '294',
            //     'product_type_name' => 'Palmhusk chips'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '300',
            //     'product_type_name' => 'NPK Mutiara 16-16-16'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '301',
            //     'product_type_name' => 'Coconut Charchoal for BBQ'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '302',
            //     'product_type_name' => 'Coconut Charchoal for Sisha'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '310',
            //     'product_type_name' => 'Dessicated Coconut High Fat'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '311',
            //     'product_type_name' => 'Dessicated Coconut Low Fat'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '312',
            //     'product_type_name' => 'VCO'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '313',
            //     'product_type_name' => 'Minyak Kelapa'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '320',
            //     'product_type_name' => 'Cangkang Sawit '
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '321',
            //     'product_type_name' => 'Wood Pellet'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '330',
            //     'product_type_name' => 'Rice Husk /Sekam Padi'
            // ],
            // [
            //     'main_category_id' => '1', 'code'            => '331',
            //     'product_type_name' => 'Rice Husk Pellet'
            // ],
        ]);
    }
}
