<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'order_token' => $this->faker->uuid(),
            'event_id' => Event::factory(),
            'consumer_name' => $this->faker->name(),
            'consumer_email' => $this->faker->safeEmail(),
            'consumer_whatsapp' => $this->faker->phoneNumber(),
            'consumer_identity_type' => 'KTP',
            'consumer_identity_number' => $this->faker->numerify('################'),
            'subtotal' => $this->faker->numberBetween(100000, 1000000),
            'discount_amount' => 0,
            'total_amount' => $this->faker->numberBetween(100000, 1000000),
            'payment_status' => 'pending',
            'payment_method' => null,
            'paid_at' => null,
            'expires_at' => now()->addMinutes(30),
        ];
    }
}
