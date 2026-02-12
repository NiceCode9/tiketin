<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PromoCode>
 */
class PromoCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'event_id' => Event::factory(),
            'discount_type' => $this->faker->randomElement(['percentage', 'fixed']),
            'discount_value' => $this->faker->numberBetween(10, 50), // For fixed, this might be small, but ok for dummy
            'max_uses' => $this->faker->numberBetween(10, 100),
            'used_count' => 0,
            'valid_from' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'valid_until' => $this->faker->dateTimeBetween('now', '+1 month'),
            'is_active' => true,
        ];
    }
}
