<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\Order;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\VenueSection;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\TicketService;
use App\Services\PromoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event as EventFacade;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $orderService;
    protected TicketService $ticketService;
    protected PaymentService $paymentService;
    protected PromoService $promoService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->ticketService = $this->mock(TicketService::class);
        $this->paymentService = $this->mock(PaymentService::class);
        $this->promoService = $this->mock(PromoService::class);
        
        $this->orderService = new OrderService(
            $this->ticketService,
            $this->paymentService, // Mocked dependencies
            $this->promoService   // Mocked dependencies
        );
    }

    public function test_can_create_order_successfully()
    {
        // Arrange
        $event = Event::factory()->create(['status' => 'published']);
        $category = TicketCategory::factory()->create([
            'event_id' => $event->id,
            'price' => 100000,
            'quota' => 100
        ]);

        $orderData = [
            'event_id' => $event->id,
            'consumer_name' => 'John Doe',
            'consumer_email' => 'john@example.com',
            'consumer_whatsapp' => '08123456789',
            'consumer_identity_type' => 'ktp',
            'consumer_identity_number' => '1234567890',
            'items' => [
                [
                    'ticket_category_id' => $category->id,
                    'quantity' => 2
                ]
            ]
        ];
        
        // Act
        $order = $this->orderService->createOrder($orderData);

        // Assert
        $this->assertDatabaseHas('orders', [
            'email' => 'john@example.com',
            'total_amount' => 200000
        ]);
        
        $this->assertDatabaseHas('order_items', [
            'ticket_category_id' => $category->id,
            'quantity' => 2,
            'unit_price' => 100000,
            'subtotal' => 200000
        ]);
    }
    
    public function test_cannot_create_order_if_quota_exceeded()
    {
        // Arrange
        $event = Event::factory()->create(['status' => 'published']);
        $category = TicketCategory::factory()->create([
            'event_id' => $event->id,
            'price' => 100000,
            'quota' => 1
        ]);

        $orderData = [
            'event_id' => $event->id,
            'consumer_name' => 'John Doe',
            'consumer_email' => 'john@example.com',
            'consumer_whatsapp' => '08123456789',
            'consumer_identity_type' => 'ktp',
            'consumer_identity_number' => '1234567890',
            'items' => [
                [
                    'ticket_category_id' => $category->id,
                    'quantity' => 2 // Requesting more than available quota
                ]
            ]
        ];

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Not enough tickets available');
        
        $this->orderService->createOrder($orderData);
    }
}
