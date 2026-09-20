<?php

namespace Database\Seeders;

use App\Models\Sub_Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Sub_CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sub_categories')->insert([
            // Main Category 1
            ['main_category_id' => '1', 'code' => '0', 'category_name' => 'Serat Halus'],
            ['main_category_id' => '1', 'code' => '1', 'category_name' => 'Serat Kasar'],

            // Main Category 2
            ['main_category_id' => '2', 'code' => '0', 'category_name' => 'Blok Padat'],
            ['main_category_id' => '2', 'code' => '1', 'category_name' => 'Bubuk Halus'],

            // Main Category 3
            ['main_category_id' => '3', 'code' => '0', 'category_name' => 'Campuran Tanah'],
            ['main_category_id' => '3', 'code' => '1', 'category_name' => 'Media Tanam'],


            // // kelapa & turunannya
            // [
            //     'main_category_id' => '1',
            //     'code' => '0',
            //     'category_name' => 'Buah Kelapa',
            // ],
            // [
            //     'main_category_id' => '1',
            //     'code' => '1',
            //     'category_name' => 'Turunan Kelapa',
            // ],
            // [
            //     'main_category_id' => '1',
            //     'code' => '2',
            //     'category_name' => 'Turunan Sabut Kelapa',
            // ],
            // [
            //     'main_category_id' => '1',
            //     'code' => '3',
            //     'category_name' => 'Turunan Kelapa Sawit',
            // ],
            // [
            //     'main_category_id' => '1',
            //     'code' => '4',
            //     'category_name' => 'Turunan Sabut Kelapa Sawit',
            // ],
            // [
            //     'main_category_id' => '1',
            //     'code' => '9',
            //     'category_name' => 'Turunan Kelapa Lainnya',
            // ],

            // // karet olahan
            // [
            //     'main_category_id' => '2',
            //     'code' => '0',
            //     'category_name' => 'Rubber Raw Material',
            // ],
            // [
            //     'main_category_id' => '2',
            //     'code' => '1',
            //     'category_name' => 'Produk Turunan Karet',
            // ],

            // // biomassa
            // [
            //     'main_category_id' => '3',
            //     'code' => '0',
            //     'category_name' => 'Biomassa dari Kelapa sawit',
            // ],
            // [
            //     'main_category_id' => '3',
            //     'code' => '1',
            //     'category_name' => 'Biomassa dari bahan kayu',
            // ],
            // [
            //     'main_category_id' => '3',
            //     'code' => '2',
            //     'category_name' => 'Biomassa dari bahan sekam',
            // ],
            // [
            //     'main_category_id' => '3',
            //     'code' => '3',
            //     'category_name' => 'Biomassa dari bahan kopi',
            // ],


            // // Hasil Alam
            // [
            //     'main_category_id' => '4',
            //     'code' => '0',
            //     'category_name' => 'Buah-buahan',
            // ],
            // [
            //     'main_category_id' => '4',
            //     'code' => '1',
            //     'category_name' => 'Rempah-rempah',
            // ],
            // [
            //     'main_category_id' => '4',
            //     'code' => '2',
            //     'category_name' => 'Umbi-umbian',
            // ],

            // // Kebutuhan Pertanian & Hobi
            // [
            //     'main_category_id' => '5',
            //     'code' => '0',
            //     'category_name' => 'Media Tanam Olahan',
            // ],
            // [
            //     'main_category_id' => '5',
            //     'code' => '1',
            //     'category_name' => 'Kebutuhan Tanaman Anggrek (ex: pine bark, peat moss)',
            // ],
            // [
            //     'main_category_id' => '5',
            //     'code' => '2',
            //     'category_name' => 'Pupuk',
            // ],
            // [
            //     'main_category_id' => '5',
            //     'code' => '3',
            //     'category_name' => 'Kebutuhan Pembuatan Kompos',
            // ],

            // // Peternakan dan Pet Suplies
            // [
            //     'main_category_id' => '6',
            //     'code' => '0',
            //     'category_name' => 'Kebutuhan Kandang',
            // ],
            // [
            //     'main_category_id' => '6',
            //     'code' => '1',
            //     'category_name' => 'Pakan',
            // ],
            // [
            //     'main_category_id' => '6',
            //     'code' => '2',
            //     'category_name' => 'Obat-obatan',
            // ],
            // [
            //     'main_category_id' => '6',
            //     'code' => '3',
            //     'category_name' => 'Mainan & Perlengkapan Perawatan',
            // ],
            // [
            //     'main_category_id' => '6',
            //     'code' => '4',
            //     'category_name' => 'Hewan Hidup',
            // ],

            // // mesin
            // [
            //     'main_category_id' => '7',
            //     'code' => '0',
            //     'category_name' => 'Mesin Pengolahan Kelapa',
            // ],
            // [
            //     'main_category_id' => '7',
            //     'code' => '1',
            //     'category_name' => 'Mesin Pengolahan Biomassa',
            // ],
            // [
            //     'main_category_id' => '7',
            //     'code' => '2',
            //     'category_name' => 'Mesin Pengolahan Pakan Ternak',
            // ],

            // // otherrrs
            // [
            //     'main_category_id' => '8',
            //     'code' => '9',
            //     'category_name' => 'other',
            // ],

            // [
            //     'main_category_id' => '9',
            //     'code' => '9',
            //     'category_name' => 'other',
            // ],
        ]);



        // Sub_Category::insert([
        //     [
        //         'id'            => '100',
        //         'category_name' => '100% Cocopeat High EC Tanpa Ayak'
        //     ],
        //     [
        //         'id'            => '101',
        //         'category_name' => '100% Cocopeat High EC Ayak 0,5'
        //     ],
        //     [
        //         'id'            => '102',
        //         'category_name' => '100% Cocopeat High EC Ayak 0,3'
        //     ],
        //     [
        //         'id'            => '103',
        //         'category_name' => '100% Cocopeat Low EC Tanpa Ayak'
        //     ],
        //     [
        //         'id'            => '104',
        //         'category_name' => '100% Cocopeat Low EC Ayak 0,5'
        //     ],
        //     [
        //         'id'            => '105',
        //         'category_name' => '100% Cocopeat Low EC Ayak 0,3'
        //     ],
        //     [
        //         'id'            => '106',
        //         'category_name' => '70% Cocopeat  Low EC 30% Cocohusk Tanpa Ayak'
        //     ],
        //     [
        //         'id'            => '107',
        //         'category_name' => '70% Cocopeat  Low EC 30% Cocohusk Ayak 0,5'
        //     ],
        //     [
        //         'id'            => '108',
        //         'category_name' => '70% Cocopeat  Low EC 30% Cocohusk Ayak 0,3'
        //     ],
        //     [
        //         'id'            => '109',
        //         'category_name' => '60% Cocopeat  Low EC 40% Cocohusk Tanpa Ayak'
        //     ],
        //     [
        //         'id'            => '110',
        //         'category_name' => '60% Cocopeat  Low EC 40% Cocohusk Ayak 0,5'
        //     ],
        //     [
        //         'id'            => '111',
        //         'category_name' => '60% Cocopeat  Low EC 40% Cocohusk Ayak 0,3'
        //     ],
        //     [
        //         'id'            => '112',
        //         'category_name' => '50% Cocopeat  Low EC 50% Cocohusk Tanpa Ayak'
        //     ],
        //     [
        //         'id'            => '113',
        //         'category_name' => '50% Cocopeat  Low EC 50% Cocohusk Ayak 0,5'
        //     ],
        //     [
        //         'id'            => '114',
        //         'category_name' => '50% Cocopeat  Low EC 50% Cocohusk Ayak 0,3'
        //     ],
        //     [
        //         'id'            => '115',
        //         'category_name' => '50% Cocopeat  Low EC 50% Cocofiber'
        //     ],
        //     [
        //         'id'            => '116',
        //         'category_name' => '40% Cocopeat  Low EC 60% Cocohusk Tanpa Ayak'
        //     ],
        //     [
        //         'id'            => '117',
        //         'category_name' => '40% Cocopeat  Low EC 60% Cocohusk Ayak 0,5'
        //     ],
        //     [
        //         'id'            => '118',
        //         'category_name' => '40% Cocopeat  Low EC 60% Cocohusk Ayak 0,3'
        //     ],
        //     [
        //         'id'            => '119',
        //         'category_name' => '60% Cocopeat  Low EC 40% Perlite Tanpa Ayak'
        //     ],
        //     [
        //         'id'            => '120',
        //         'category_name' => '60% Cocopeat  Low EC 40% Perlite Ayak 0,5'
        //     ],
        //     [
        //         'id'            => '121',
        //         'category_name' => '60% Cocopeat  Low EC 40% Perlite Ayak 0,3'
        //     ],
        //     [
        //         'id'            => '122',
        //         'category_name' => '50% Cocopeat  Low EC 50% Perlite Tanpa Ayak'
        //     ],
        //     [
        //         'id'            => '123',
        //         'category_name' => '50% Cocopeat  Low EC 50% Perlite Ayak 0,5'
        //     ],
        //     [
        //         'id'            => '124',
        //         'category_name' => '50% Cocopeat  Low EC 50% Perlite Ayak 0,3'
        //     ],
        //     [
        //         'id'            => '125',
        //         'category_name' => '40% Cocopeat  Low EC 60% Perlite Tanpa Ayak'
        //     ],
        //     [
        //         'id'            => '126',
        //         'category_name' => '40% Cocopeat  Low EC 60% Perlite Ayak 0,5'
        //     ],
        //     [
        //         'id'            => '127',
        //         'category_name' => '40% Cocopeat  Low EC 60% Perlite Ayak 0,3'
        //     ],
        //     [
        //         'id'            => '128',
        //         'category_name' => 'Cocopeat Shredded'
        //     ],
        //     [
        //         'id'            => '140',
        //         'category_name' => 'Cocofiber Ekspor'
        //     ],
        //     [
        //         'id'            => '141',
        //         'category_name' => 'Cocofiber Lokal'
        //     ],
        //     [
        //         'id'            => '142',
        //         'category_name' => 'Cocofiber Bale Ekspor'
        //     ],
        //     [
        //         'id'            => '143',
        //         'category_name' => 'Cocofiber Bale Lokal'
        //     ],
        //     [
        //         'id'            => '150',
        //         'category_name' => 'Cocohusk Low EC'
        //     ],
        //     [
        //         'id'            => '151',
        //         'category_name' => 'Cocohusk High EC'
        //     ],
        //     [
        //         'id'            => '160',
        //         'category_name' => 'Cocochips Low EC'
        //     ],
        //     [
        //         'id'            => '161',
        //         'category_name' => 'Cocochips High EC'
        //     ],
        //     [
        //         'id'            => '170',
        //         'category_name' => 'Cocosheet Tipis <1 cm'
        //     ],
        //     [
        //         'id'            => '171',
        //         'category_name' => 'Cocosheet Tebal 1 cm'
        //     ],
        //     [
        //         'id'            => '172',
        //         'category_name' => 'Cocosheet Tebal 2 cm'
        //     ],
        //     [
        //         'id'            => '173',
        //         'category_name' => 'Cocosheet Tebal 3 cm'
        //     ],
        //     [
        //         'id'            => '174',
        //         'category_name' => 'Cocosheet Tebal 5 cm'
        //     ],
        //     [
        //         'id'            => '175',
        //         'category_name' => 'Cocosheet Tebal 8 cm'
        //     ],
        //     [
        //         'id'            => '176',
        //         'category_name' => 'Cocosheet Tebal 10 cm'
        //     ],
        //     [
        //         'id'            => '177',
        //         'category_name' => 'Cocosheet Tebal 30 cm'
        //     ],
        //     [
        //         'id'            => '190',
        //         'category_name' => 'Coco Mulsa Bulat'
        //     ],
        //     [
        //         'id'            => '191',
        //         'category_name' => 'Coco Mulsa Kotak'
        //     ],
        //     [
        //         'id'            => '200',
        //         'category_name' => 'Cocorope - Benang Nylon'
        //     ],
        //     [
        //         'id'            => '201',
        //         'category_name' => 'Cocorope - Benang Cotton'
        //     ],
        //     [
        //         'id'            => '210',
        //         'category_name' => 'Coco Bristle Warna Natural'
        //     ],
        //     [
        //         'id'            => '211',
        //         'category_name' => 'Coco Bristle Warna Hitam'
        //     ],
        //     [
        //         'id'            => '220',
        //         'category_name' => 'Coir Geotextile / Cocomesh'
        //     ],
        //     [
        //         'id'            => '221',
        //         'category_name' => 'Coir Blanket isi Cocofiber'
        //     ],
        //     [
        //         'id'            => '222',
        //         'category_name' => 'Coir Blanket isi Jerami'
        //     ],
        //     [
        //         'id'            => '223',
        //         'category_name' => 'Coir Roll'
        //     ],
        //     [
        //         'id'            => '224',
        //         'category_name' => 'Coir Mat'
        //     ],
        //     [
        //         'id'            => '230',
        //         'category_name' => 'Cocopot Press Kotak'
        //     ],
        //     [
        //         'id'            => '231',
        //         'category_name' => 'Cocopot Press Persegi Panjang'
        //     ],
        //     [
        //         'id'            => '232',
        //         'category_name' => 'Cocopot Press Bulat'
        //     ],
        //     [
        //         'id'            => '233',
        //         'category_name' => 'Cocopot Press Setengah Lingkaran'
        //     ],
        //     [
        //         'id'            => '234',
        //         'category_name' => 'Cocopot Press Hexagonal'
        //     ],
        //     [
        //         'id'            => '235',
        //         'category_name' => 'Cocopot Press Basket'
        //     ],
        //     [
        //         'id'            => '236',
        //         'category_name' => 'Cocopot Press Lainnya'
        //     ],
        //     [
        //         'id'            => '250',
        //         'category_name' => 'Cocopot Anyam Kotak'
        //     ],
        //     [
        //         'id'            => '251',
        //         'category_name' => 'Cocopot Anyam Persegi Panjang'
        //     ],
        //     [
        //         'id'            => '252',
        //         'category_name' => 'Cocopot Anyam Bulat'
        //     ],
        //     [
        //         'id'            => '253',
        //         'category_name' => 'Cocopot Anyam Setengah Lingkaran'
        //     ],
        //     [
        //         'id'            => '254',
        //         'category_name' => 'Cocopot Anyam Hexagonal'
        //     ],
        //     [
        //         'id'            => '255',
        //         'category_name' => 'Cocopot Anyam Cone'
        //     ],
        //     [
        //         'id'            => '256',
        //         'category_name' => 'Cocopot Anyam Basket'
        //     ],
        //     [
        //         'id'            => '257',
        //         'category_name' => 'Cocopot Anyam Lainnya'
        //     ],
        //     [
        //         'id'            => '270',
        //         'category_name' => 'Cocopot Wiremesh Kotak'
        //     ],
        //     [
        //         'id'            => '271',
        //         'category_name' => 'Cocopot Wiremesh Persegi Panjang'
        //     ],
        //     [
        //         'id'            => '272',
        //         'category_name' => 'Cocopot Wiremesh Bulat'
        //     ],
        //     [
        //         'id'            => '273',
        //         'category_name' => 'Cocopot Wiremesh Setengah Lingkaran Dengan Sisi Gantung'
        //     ],
        //     [
        //         'id'            => '274',
        //         'category_name' => 'Cocopot Wiremesh Setengah Lingkara Tanpa Sisi Gantung'
        //     ],
        //     [
        //         'id'            => '275',
        //         'category_name' => 'Cocopot Wiremesh Hexagonal'
        //     ],
        //     [
        //         'id'            => '276',
        //         'category_name' => 'Cocopot Wiremesh Hati'
        //     ],
        //     [
        //         'id'            => '277',
        //         'category_name' => 'Cocopot Wiremesh Cone'
        //     ],
        //     [
        //         'id'            => '278',
        //         'category_name' => 'Cocopot Wiremesh Basket'
        //     ],
        //     [
        //         'id'            => '279',
        //         'category_name' => 'Cocopot Wiremesh Lainnya'
        //     ],
        //     [
        //         'id'            => '290',
        //         'category_name' => 'Palmrope'
        //     ],
        //     [
        //         'id'            => '291',
        //         'category_name' => 'Palmpeat'
        //     ],
        //     [
        //         'id'            => '292',
        //         'category_name' => 'Palmmesh'
        //     ],
        //     [
        //         'id'            => '293',
        //         'category_name' => 'Palmfiber'
        //     ],
        //     [
        //         'id'            => '294',
        //         'category_name' => 'Palmhusk chips'
        //     ],
        //     [
        //         'id'            => '300',
        //         'category_name' => 'NPK Mutiara 16-16-16'
        //     ],
        //     [
        //         'id'            => '301',
        //         'category_name' => 'Coconut Charchoal for BBQ'
        //     ],
        //     [
        //         'id'            => '302',
        //         'category_name' => 'Coconut Charchoal for Sisha'
        //     ],
        //     [
        //         'id'            => '310',
        //         'category_name' => 'Dessicated Coconut High Fat'
        //     ],
        //     [
        //         'id'            => '311',
        //         'category_name' => 'Dessicated Coconut Low Fat'
        //     ],
        //     [
        //         'id'            => '312',
        //         'category_name' => 'VCO'
        //     ],
        //     [
        //         'id'            => '313',
        //         'category_name' => 'Minyak Kelapa'
        //     ],
        //     [
        //         'id'            => '320',
        //         'category_name' => 'Cangkang Sawit '
        //     ],
        //     [
        //         'id'            => '321',
        //         'category_name' => 'Wood Pellet'
        //     ],
        //     [
        //         'id'            => '330',
        //         'category_name' => 'Rice Husk /Sekam Padi'
        //     ],
        //     [
        //         'id'            => '331',
        //         'category_name' => 'Rice Husk Pellet'
        //     ],
        //     [
        //         'id'            => '400',
        //         'category_name' => 'Kulit Pinus / Pine Bark'
        //     ],
        //     [
        //         'id'            => '500',
        //         'category_name' => 'Media Tanam Jadi Base Cocopeat'
        //     ],
        //     [
        //         'id'            => '501',
        //         'category_name' => 'Media Tanam Jadi Base Soil'
        //     ],
        //     [
        //         'id'            => '502',
        //         'category_name' => 'Media Tanam Jadi Mix Cocopeat & Soil'
        //     ],
        //     [
        //         'id'            => '510',
        //         'category_name' => 'Media Tanam Anggrek Pakis Kotak Dengan Kawat'
        //     ],
        //     [
        //         'id'            => '511',
        //         'category_name' => 'Media Tanam Anggrek Pakis Kotak Tanpa Kawat'
        //     ],
        //     [
        //         'id'            => '512',
        //         'category_name' => 'Media Tanam Anggrek Cocosheet Kotak Dengan Kawat'
        //     ],
        //     [
        //         'id'            => '513',
        //         'category_name' => 'Media Tanam Anggrek Cocosheet Kotak Tanpa Kawat'
        //     ],
        //     [
        //         'id'            => '514',
        //         'category_name' => 'Media Tanam Anggrek Pakis Cacah'
        //     ],
        //     [
        //         'id'            => '515',
        //         'category_name' => 'Media Tanam Anggrek Lumut'
        //     ],
        //     [
        //         'id'            => '516',
        //         'category_name' => 'Media Tanam Anggrek Arang Kayu'
        //     ],
        //     [
        //         'id'            => '530',
        //         'category_name' => 'Dolomite'
        //     ],
        //     [
        //         'id'            => '531',
        //         'category_name' => 'Calsium Sulphate'
        //     ],
        //     [
        //         'id'            => '532',
        //         'category_name' => 'Perlite'
        //     ],
        //     [
        //         'id'            => '540',
        //         'category_name' => 'EM4 Pertanian'
        //     ],
        //     [
        //         'id'            => '541',
        //         'category_name' => 'EM4 Perikanan'
        //     ],
        //     [
        //         'id'            => '542',
        //         'category_name' => 'EM4 Peternakan'
        //     ],
        //     [
        //         'id'            => '543',
        //         'category_name' => 'EM4 Air Limbah'
        //     ],
        //     [
        //         'id'            => '550',
        //         'category_name' => 'Tetes Tebu'
        //     ],
        //     [
        //         'id'            => '551',
        //         'category_name' => 'Pupuk Organik Cair'
        //     ],
        //     [
        //         'id'            => '600',
        //         'category_name' => 'Kapulaga'
        //     ],
        //     [
        //         'id'            => '601',
        //         'category_name' => 'Lada Hitam'
        //     ],
        //     [
        //         'id'            => '602',
        //         'category_name' => 'Cengkeh'
        //     ],
        //     [
        //         'id'            => '603',
        //         'category_name' => 'Kayu Manis'
        //     ],
        //     [
        //         'id'            => '700',
        //         'category_name' => 'Mesin Santan'
        //     ],
        //     [
        //         'id'            => '701',
        //         'category_name' => 'Mesin Screw Press Santan M02A'
        //     ],
        //     [
        //         'id'            => '702',
        //         'category_name' => 'Mesin Screw Press Santan M02B'
        //     ],
        //     [
        //         'id'            => '710',
        //         'category_name' => 'Mesin Centrifuge (VCO) MV03'
        //     ],
        //     [
        //         'id'            => '711',
        //         'category_name' => 'Mesin Centrifuge (VCO) MV03M'
        //     ],
        //     [
        //         'id'            => '712',
        //         'category_name' => 'Mesin Mixer Pendingin Santan'
        //     ],
        //     [
        //         'id'            => '713',
        //         'category_name' => 'Mesin Mixer Pemanas Santan'
        //     ],
        //     [
        //         'id'            => '714',
        //         'category_name' => 'Mesin Filter VCO - 2 housing'
        //     ],
        //     [
        //         'id'            => '720',
        //         'category_name' => 'Mesin Plan Minyak Goreng 2 Tabung'
        //     ],
        //     [
        //         'id'            => '721',
        //         'category_name' => 'Mesin Filter Minyak Goreng - 2 housing'
        //     ],
        //     [
        //         'id'            => '730',
        //         'category_name' => 'Mesin Pengurai Sabut Kelapa S04'
        //     ],
        //     [
        //         'id'            => '731',
        //         'category_name' => 'Mesin Pengurai Sabut Kelapa S04 M'
        //     ],
        //     [
        //         'id'            => '732',
        //         'category_name' => 'Mesin Penyaring/Pengayak S05'
        //     ],
        //     [
        //         'id'            => '733',
        //         'category_name' => 'Mesin Penyaring/Pengayak S05M'
        //     ],
        //     [
        //         'id'            => '734',
        //         'category_name' => 'Mesin Pembersih Serat (Willowing)'
        //     ],
        //     [
        //         'id'            => '735',
        //         'category_name' => 'Mesin Pemintal Tali Sabut S07A'
        //     ],
        //     [
        //         'id'            => '736',
        //         'category_name' => 'Mesin Pemintal Tali Sabut S07B'
        //     ],
        //     [
        //         'id'            => '737',
        //         'category_name' => 'Mesin Feeding'
        //     ],
        //     [
        //         'id'            => '738',
        //         'category_name' => 'Mesin Penggulung Tali'
        //     ],
        //     [
        //         'id'            => '790',
        //         'category_name' => 'Mesin Washpod'
        //     ],

        // ]);
    }
}
