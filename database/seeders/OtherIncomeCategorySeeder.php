<?php

namespace Database\Seeders;

use App\Models\OtherIncomeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OtherIncomeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Interest Bank',
            'Refund',
            'BPJS Skillbridge',
            'Pajak Masukan',
            'Others',
        ];

        foreach ($categories as $category) {
            OtherIncomeCategory::create([
                'category_name' => $category,
                'category_slug' => Str::slug($category)
            ]);
        }
    }
}
