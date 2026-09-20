<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Channel::insert([
            ['channel_name' => 'Alibaba'],
            ['channel_name' => 'Blibli'],
            ['channel_name' => 'Bukalapak'],
            ['channel_name' => 'Email'],
            ['channel_name' => 'Facebook'],
            ['channel_name' => 'Phone'],
            ['channel_name' => 'Shopee'],
            ['channel_name' => 'Tokopedia'],
            ['channel_name' => 'Telegram'],
            ['channel_name' => 'WhatsApp'],
            ['channel_name' => 'Visiting'],
            ['channel_name' => 'Instagram'],
            ['channel_name' => 'Website'],

            // ['channel_name' => 'Alibaba'],
            // ['channel_name' => 'Blibli'],
            // ['channel_name' => 'Bukalapak'],
            // ['channel_name' => 'Email'],
            // ['channel_name' => 'Etsy'],
            // ['channel_name' => 'Facebook'],
            // ['channel_name' => 'Mbiz market'],
            // ['channel_name' => 'Phone'],
            // ['channel_name' => 'Shopee'],
            // ['channel_name' => 'Stone ADD'],
            // ['channel_name' => 'Stone Contact'],
            // ['channel_name' => 'Tokopedia'],
            // ['channel_name' => 'Telegram'],
            // ['channel_name' => 'WhatsApp'],
            // ['channel_name' => 'Arsitag'],
            // ['channel_name' => 'Visiting'],
            // ['channel_name' => 'Instagram'],
            // ['channel_name' => 'Goforworld'],
            // ['channel_name' => 'Website'],
        ]);

        Channel::insert([
            'id'           => 99,
            'channel_name' => 'Others'
        ]);
    }
}
