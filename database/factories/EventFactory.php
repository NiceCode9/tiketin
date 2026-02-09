<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'venue_id' => null,
            'name' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'event_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'event_end_date' => $this->faker->dateTimeBetween('+1 year', '+2 years'),
            'status' => 'published',
            'has_assigned_seating' => false,
            'wristband_exchange_start' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'wristband_exchange_end' => $this->faker->dateTimeBetween('now', '+1 week'),
        ];
    }
}
