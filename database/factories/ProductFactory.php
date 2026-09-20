<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku'              => fake()->randomNumber(9, true), // bisa menyebabkan error saat seeding karena mengeluarkan angka yang sama
            'brand'            => fake()->word(),
            'specification_id' => '001',
            'packaging_id'     => '002',
            'category_id'      => '100',
            'supplier_id'      => '003',
            'unit_id'          => fake()->numberBetween(1, 2),
            'barcode'          => fake()->isbn13(),
            'note'             => fake()->paragraph(),
            'qty'              => 0,
            'harga_rata_rata'  => 0,
            'harga_tertinggi'  => 0,
        ];
    }
}
