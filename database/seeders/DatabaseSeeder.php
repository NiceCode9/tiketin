<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Event;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Roles & Permissions
        $this->call([
            RolesAndPermissionsSeeder::class,
            EventCategorySeeder::class,
        ]);

        // 2. Create Super Admin
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
        $superAdmin->assignRole('super_admin');

        // 3. Create 2 Clients
        $clients = Client::factory(2)->create();

        foreach ($clients as $index => $client) {
            // Create Client Admin
            $clientAdmin = User::factory()->create([
                'name' => 'Admin '.$client->name,
                'email' => 'admin'.($index + 1).'@client.com',
                'password' => bcrypt('password'),
                'client_id' => $client->id,
            ]);
            $clientAdmin->assignRole('client_admin');

            // Create Venues for this client
            $venues = Venue::factory(2)->create([
                'client_id' => $client->id,
                'city' => 'Jakarta',
            ]);

            // Create 5 Events per Client
            $events = Event::factory(5)->create([
                'client_id' => $client->id,
                'venue_id' => $venues->random()->id,
                'status' => 'published',
            ]);

            foreach ($events as $event) {
                // Create Ticket Categories (VIP, Regular)
                TicketCategory::factory()->create([
                    'event_id' => $event->id,
                    'name' => 'VIP',
                    'price' => 1000000,
                    'quota' => 100,
                ]);

                TicketCategory::factory()->create([
                    'event_id' => $event->id,
                    'name' => 'Regular',
                    'price' => 500000,
                    'quota' => 500,
                ]);

                // Create Promo Codes
                PromoCode::factory(2)->create([
                    'event_id' => $event->id,
                ]);

                // Create some dummy orders for the first event
                // if ($event->id === $events->first()->id) {
                //     Order::factory(5)->create([
                //         'event_id' => $event->id,
                //         'payment_status' => 'paid',
                //     ])->each(function ($order) {
                //         // Ticket creation logic handled in OrderObserver usually,
                //         // but here we might need to manually create tickets if factories don't assume observers run
                //         // or if OrderFactory doesn't create items.
                //         // Let's assume generic factory usage for now.
                //     });
                // }
            }
        }
    }
}
