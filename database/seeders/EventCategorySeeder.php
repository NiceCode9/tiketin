<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Konser',
                'slug' => 'konser',
                'icon' => 'fas fa-music',
                'description' => 'Konser musik, festival, dan pertunjukan live.',
            ],
            [
                'name' => 'Stand Up',
                'slug' => 'standup',
                'icon' => 'fas fa-laugh',
                'description' => 'Pertunjukan komedi tunggal dan festival tawa.',
            ],
            [
                'name' => 'Workshop',
                'slug' => 'workshop',
                'icon' => 'fas fa-chalkboard-teacher',
                'description' => 'Kelas pelatihan, seminar, dan workshop edukatif.',
            ],
            [
                'name' => 'Olahraga',
                'slug' => 'olahraga',
                'icon' => 'fas fa-running',
                'description' => 'Pertandingan olahraga, turnamen, dan kegiatan fisik.',
            ],
            [
                'name' => 'Seminar',
                'slug' => 'seminar',
                'icon' => 'fas fa-microphone',
                'description' => 'Seminar bisnis, talkshow, dan konferensi.',
            ],
            [
                'name' => 'Pameran',
                'slug' => 'pameran',
                'icon' => 'fas fa-image',
                'description' => 'Pameran seni, bazaar, dan expo.',
            ],
        ];

        foreach ($categories as $category) {
            EventCategory::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
