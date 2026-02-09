<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\TicketCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'order_id' => Order::factory(),
            'ticket_category_id' => TicketCategory::factory(),
            'seat_id' => null,
            'consumer_name' => $this->faker->name(),
            'consumer_identity_type' => 'KTP',
            'consumer_identity_number' => $this->faker->numerify('################'),
            'status' => 'pending_payment',
            'checksum' => $this->faker->md5(),
        ];
    }
}
