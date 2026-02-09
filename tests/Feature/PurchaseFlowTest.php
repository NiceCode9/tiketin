<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_event_listing_page()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('events.index');
    }

    public function test_can_view_event_detail_page()
    {
        $event = Event::factory()->create(['status' => 'published']);
        
        $response = $this->get("/events/{$event->slug}");
        
        $response->assertStatus(200);
        $response->assertSee($event->name);
    }

    public function test_can_create_order()
    {
        $event = Event::factory()->create(['status' => 'published']);
        $category = TicketCategory::factory()->create([
            'event_id' => $event->id,
            'price' => 100000,
            'quota' => 100
        ]);

        $response = $this->post("/events/{$event->slug}/order", [
            'consumer_name' => 'Jane Doe',
            'consumer_email' => 'jane@example.com',
            'consumer_whatsapp' => '08123456789',
            'consumer_identity_type' => 'ktp',
            'consumer_identity_number' => '1234567890123456',
            'items' => [
                [
                    'ticket_category_id' => $category->id,
                    'quantity' => 1
                ]
            ]
        ]);

        $response->assertRedirect();
        // Should redirect to checkout page
        $this->assertDatabaseHas('orders', ['consumer_email' => 'jane@example.com']);
    }
}
