<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketCategory>
 */
class TicketCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(50000, 500000),
            'quota' => $this->faker->numberBetween(50, 500),
            'sold_count' => $this->faker->numberBetween(0, 500),
            'is_seated' => false,
            'venue_section_id' => null,
        ];
    }
}
