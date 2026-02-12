<?php

namespace Database\Factories;

use App\Models\VenueSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seat>
 */
class SeatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $row = $this->faker->randomLetter();
        $number = $this->faker->numberBetween(1, 20);
        
        return [
            'venue_section_id' => VenueSection::factory(),
            'row_label' => $row,
            'seat_number' => (string) $number,
            'is_accessible' => $this->faker->boolean(5),
            'status' => 'available',
        ];
    }
}
