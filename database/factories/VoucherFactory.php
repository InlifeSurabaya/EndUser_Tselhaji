<?php

namespace Database\Factories;

use App\Enum\DiscountTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discountType = fake()->randomElement(DiscountTypeEnum::cases());

        return [
            'code' => fake()->unique()->regexify('[A-Z0-9]{10}'),
            'discount_value' => $discountType == DiscountTypeEnum::PERCENTEAGE ? fake()->numberBetween(5, 50) : fake()->randomElement([10000, 15000, 20000]),
            'start_date' => now(),
            'end_date' => now()->addDays(fake()->numberBetween(10, 60)),
            'discount_type' => $discountType->value,
            'usage_limit' => fake()->numberBetween(50, 200),
            'used_count' => 0,
            'is_active' => 1,
        ];
    }
}
