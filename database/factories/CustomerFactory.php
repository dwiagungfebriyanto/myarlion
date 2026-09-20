<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'id' => fake()->unique()->numberBetween(1, 100),
            'name' => fake()->name(),
            'code' => fake()->unique()->numberBetween(10000, 99999),
        ];
    }

    // public function definition(): array
    // {
    //     return [
    //         'id' => fake()->unique()->numberBetween(1, 100),
    //         'name' => fake()->name(),
    //         'code' => fake()->unique()->numberBetween(1, 10),
    //     ];
    // }
}
