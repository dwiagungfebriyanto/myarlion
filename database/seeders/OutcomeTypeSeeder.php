<?php

namespace Database\Seeders;

use App\Models\OutcomeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OutcomeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OutcomeType::insert([
            [
                'id'               => 101001,
                'name'             => 'Gross Sales',
                'outcome_group_id' => 1
            ],
            [
                'id'               => 101002,
                'name'             => 'COGS',
                'outcome_group_id' => 1
            ],
            [
                'id'               => 101003,
                'name'             => 'Gross Profit',
                'outcome_group_id' => 1
            ],
            [
                'id'               => 201001,
                'name'             => 'Bank Charges (Rek. Koran IDR)',
                'outcome_group_id' => 2
            ],
            [
                'id'               => 201002,
                'name'             => 'Bank Charges (Rek. Koran USD)',
                'outcome_group_id' => 2
            ],
            [
                'id'               => 201003,
                'name'             => 'Bi. Transfer',
                'outcome_group_id' => 2
            ],
            [
                'id'               => 201004,
                'name'             => 'Biaya Cetak Giro',
                'outcome_group_id' => 2
            ],
            [
                'id'               => 201005,
                'name'             => 'Interest',
                'outcome_group_id' => 2
            ],
            [
                'id'               => 202001,
                'name'             => 'Telp.',
                'outcome_group_id' => 3
            ],
            [
                'id'               => 202002,
                'name'             => 'Voucher HP',
                'outcome_group_id' => 3
            ],
            [
                'id'               => 202003,
                'name'             => 'Fax',
                'outcome_group_id' => 3
            ],
            [
                'id'               => 202004,
                'name'             => 'Internet',
                'outcome_group_id' => 3
            ],
            [
                'id'               => 203001,
                'name'             => 'City Transportation',
                'outcome_group_id' => 4
            ],
            [
                'id'               => 203002,
                'name'             => 'Outstation Transportation',
                'outcome_group_id' => 4
            ],
            [
                'id'               => 203003,
                'name'             => 'Accomodation',
                'outcome_group_id' => 4
            ],
            [
                'id'               => 203004,
                'name'             => 'Goods Delivery (Logistic Fee) & Loading-unloading',
                'outcome_group_id' => 4
            ],
            [
                'id'               => 204001,
                'name'             => 'Office Supplies',
                'outcome_group_id' => 5
            ],
            [
                'id'               => 204002,
                'name'             => 'Office Asset',
                'outcome_group_id' => 5
            ],
            [
                'id'               => 204003,
                'name'             => 'Selling Supplies',
                'outcome_group_id' => 5
            ],
            [
                'id'               => 205001,
                'name'             => 'Electricity',
                'outcome_group_id' => 6
            ],
            [
                'id'               => 206001,
                'name'             => 'Maintenance',
                'outcome_group_id' => 7
            ],
            [
                'id'               => 207001,
                'name'             => 'Office Rental ',
                'outcome_group_id' => 8
            ],
            [
                'id'               => 208001,
                'name'             => 'Sample Cost',
                'outcome_group_id' => 9
            ],
            [
                'id'               => 208002,
                'name'             => 'Sample Delivery Cost',
                'outcome_group_id' => 9
            ],
            [
                'id'               => 209001,
                'name'             => 'Salary',
                'outcome_group_id' => 10
            ],
            [
                'id'               => 209002,
                'name'             => 'Recruitment',
                'outcome_group_id' => 10
            ],
            [
                'id'               => 209003,
                'name'             => 'Meals, Phone, Other Allowance',
                'outcome_group_id' => 10
            ],
            [
                'id'               => 209004,
                'name'             => 'BPJS Kesehatan & other Insurance',
                'outcome_group_id' => 10
            ],
            [
                'id'               => 209005,
                'name'             => 'BPJS Ketenagakerjaan',
                'outcome_group_id' => 10
            ],
            [
                'id'               => 210001,
                'name'             => 'Google',
                'outcome_group_id' => 11
            ],
            [
                'id'               => 210002,
                'name'             => 'Tokopedia',
                'outcome_group_id' => 11
            ],
            [
                'id'               => 210003,
                'name'             => 'Shopee',
                'outcome_group_id' => 11
            ],
            [
                'id'               => 210004,
                'name'             => 'Article & Backlink',
                'outcome_group_id' => 11
            ],
            [
                'id'               => 210005,
                'name'             => 'Domain & Hosting',
                'outcome_group_id' => 11
            ],
            [
                'id'               => 210006,
                'name'             => 'Social content (pict, video, followers, comment)',
                'outcome_group_id' => 11
            ],
            [
                'id'               => 210007,
                'name'             => 'Other (Plugin, SSL, etc)',
                'outcome_group_id' => 11
            ],
            [
                'id'               => 211001,
                'name'             => 'Seminar / Training',
                'outcome_group_id' => 12
            ],
            [
                'id'               => 211002,
                'name'             => 'Membership',
                'outcome_group_id' => 12
            ],
            [
                'id'               => 211003,
                'name'             => 'Entertaint',
                'outcome_group_id' => 12
            ],
            [
                'id'               => 211004,
                'name'             => 'Neighborhood cost',
                'outcome_group_id' => 12
            ],
            [
                'id'               => 211005,
                'name'             => 'License',
                'outcome_group_id' => 12
            ],
            [
                'id'               => 211006,
                'name'             => 'Consultant',
                'outcome_group_id' => 12
            ],
            [
                'id'               => 211007,
                'name'             => 'Others',
                'outcome_group_id' => 12
            ],
        ]);
    }
}
