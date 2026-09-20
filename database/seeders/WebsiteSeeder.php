<?php

namespace Database\Seeders;

use App\Models\Website;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Website::insert([
            [
                'web_domain' => 'fibertech.com',
            ],
            [
                'web_domain' => 'naturalweave.com.au',
            ],
            [
                'web_domain' => 'bioform.id',
            ],
            [
                'web_domain' => 'ecoblock.de',
            ],
            [
                'web_domain' => 'growmix.com',
            ],
            [
                'web_domain' => 'soilproindonesia.com',
            ],

            // [
            //     'web_domain' => 'alaskandang.com',
            // ],
            // [
            //     'web_domain' => 'arlion.co.id',
            // ],
            // [
            //     'web_domain' => 'cocopeatcocofiber.com.sg',
            // ],
            // [
            //     'web_domain' => 'indonesia-cardamom.com',
            // ],
            // [
            //     'web_domain' => 'indonesiacardamoms.com',
            // ],
            // [
            //     'web_domain' => 'indonesiacocofiber.com',
            // ],
            // [
            //     'web_domain' => 'indonesiacoconutcharcoal.net',
            // ],
            // [
            //     'web_domain' => 'indonesiacocopeat.com',
            // ],
            // [
            //     'web_domain' => 'indonesiaherbspices.com',
            // ],
            // [
            //     'web_domain' => 'indonesiapinebark.com',
            // ],
            // [
            //     'web_domain' => 'indonesiawoodpellets.com',
            // ],
            // [
            //     'web_domain' => 'jualcangkangsawit.com',
            // ],
            // [
            //     'web_domain' => 'jualcocomesh.com',
            // ],
            // [
            //     'web_domain' => 'jualsapikurban.com',
            // ],
            // [
            //     'web_domain' => 'jualspeedbump.com',
            // ],
            // [
            //     'web_domain' => 'jualwoodpellet.com',
            // ],
            // [
            //     'web_domain' => 'kerjadijepanggratis.com',
            // ],
            // [
            //     'web_domain' => 'konjacchips.com',
            // ],
            // [
            //     'web_domain' => 'konjac-indonesia.com',
            // ],
            // [
            //     'web_domain' => 'mesinsabutkelapa.com',
            // ],
            // [
            //     'web_domain' => 'pawrepublic.id',
            // ],
            // [
            //     'web_domain' => 'tanami.co.id',
            // ],
            // [
            //     'web_domain' => 'washpod.id',
            // ],
            // [
            //     'web_domain' => 'washpodindonesia.com',
            // ],

        ]);
    }
}
