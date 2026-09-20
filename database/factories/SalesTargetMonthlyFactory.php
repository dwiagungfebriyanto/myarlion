<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesTargetMonthly>
 */
class SalesTargetMonthlyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'         => fake()->numberBetween(3, 8),
            'target'          => fake()->randomNumber(8),
            'month'           => date('Y-m') . '-01',
            'percentage'      => fake()->randomNumber(2),
            'estimate_profit' => fake()->randomNumber(8),
            'commission'      => fake()->randomNumber(6),
        ];
    }
}
