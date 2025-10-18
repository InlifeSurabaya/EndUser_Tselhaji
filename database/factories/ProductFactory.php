<?php

namespace Database\Factories;

use App\Models\CategoryCountryProduct;
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
            'country_id' => fake()->numberBetween(1, 250),
            'name' => 'Paket Data ' . fake()->words(2, true),
            'detail' => fake()->sentence(),
            'harga' => fake()->numberBetween(10000, 200000),
            'quota_amount' => fake()->randomElement([5, 10, 15, 20, 50]),
            'quota_type' => 'GB',
            'validity_days' => fake()->randomElement([7, 15, 30]),
            'discount' => fake()->optional(0.2)->numberBetween(5, 20),
            'is_active' => fake()->boolean(100),
        ];
    }
}
