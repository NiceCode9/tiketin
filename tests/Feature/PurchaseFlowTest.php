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
        $response->assertViewIs('home');
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
            'quota' => 100,
            'sold_count' => 0
        ]);

        $response = $this->post("/events/{$event->slug}/order", [
            'consumer_name' => 'Jane Doe',
            'consumer_email' => 'jane@example.com',
            'consumer_city' => 'Jakarta',
            'consumer_birth_date' => '1990-01-01',
            'consumer_whatsapp' => '08123456789',
            'consumer_identity_type' => 'KTP',
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

    public function test_bundling_creates_multiple_tickets()
    {
        $event = Event::factory()->create(['status' => 'published']);
        $category = TicketCategory::factory()->create([
            'event_id' => $event->id,
            'price' => 200000,
            'quota' => 100,
            'sold_count' => 0,
            'ticket_count' => 2 // This is a "Bundle 2"
        ]);

        $orderData = [
            'event_id' => $event->id,
            'consumer_name' => 'John Bundler',
            'consumer_email' => 'john@bundle.com',
            'consumer_city' => 'Jakarta',
            'consumer_birth_date' => '1990-01-01',
            'consumer_whatsapp' => '08123456788',
            'consumer_identity_type' => 'KTP',
            'consumer_identity_number' => '1234567890123455',
            'items' => [
                [
                    'ticket_category_id' => $category->id,
                    'quantity' => 1
                ]
            ]
        ];

        // Manually create order and mark as paid using OrderService
        $orderService = app(\App\Services\OrderService::class);
        $order = $orderService->createOrder($orderData);
        $orderService->markAsPaid($order, ['payment_method' => 'manual_test']);

        // Check if 2 tickets are generated
        $this->assertEquals(2, $order->tickets()->count());
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_status' => 'paid']);
    }
}
