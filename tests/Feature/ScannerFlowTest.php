<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ScannerFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure roles exist
        Role::firstOrCreate(['name' => 'wristband_exchange_officer', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'wristband_validator', 'guard_name' => 'web']);
    }

    public function test_scanner_can_access_exchange_page()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create(['client_id' => $client->id]);
        $user->assignRole('wristband_exchange_officer');

        $response = $this->actingAs($user)->get('/scanner/exchange');

        $response->assertStatus(200);
        $response->assertViewIs('scanner.exchange.index');
    }

    public function test_non_scanner_cannot_access_exchange_page()
    {
        $user = User::factory()->create();
        // Not assigning role

        $response = $this->actingAs($user)->get('/scanner/exchange');

        $response->assertStatus(403); // Or redirect depending on middleware
    }

    public function test_can_scan_ticket_for_exchange()
    {
        $client = Client::factory()->create();
        $user = User::factory()->create(['client_id' => $client->id]);
        $user->assignRole('wristband_exchange_officer');

        $event = Event::factory()->create(['client_id' => $client->id, 'status' => 'published']);
        $order = Order::factory()->create(['event_id' => $event->id, 'payment_status' => 'paid']); // Ensure paid
        $ticket = Ticket::factory()->create([
            'order_id' => $order->id,
            'ticket_category_id' => \App\Models\TicketCategory::factory()->create(['event_id' => $event->id])->id,
            'status' => 'active'
        ]);

        // Mock TicketService generating valid QR data if needed, or rely on actual implementation
        // For integration test, we simulate the scan endpoint call with QR code string
        
        $qrCode = "ticket:{$ticket->ticket_code}:mocked_checksum"; // Adjustment needed based on actual format

        // We might need to mock the service validation if checksum is complex
        // But for feature test, let's assume valid input or mock service
        
        // This test might be fragile without mocking services, so focusing on route access is safer for now
        // Or properly mocking the service response
        
        $this->mock(\App\Services\TicketService::class, function ($mock) use ($ticket) {
            $mock->shouldReceive('validateQR')->andReturn([
                'valid' => true,
                'ticket' => $ticket,
                'message' => 'Valid ticket'
            ]);
        });
        
        $response = $this->actingAs($user)->postJson('/scanner/exchange/scan', [
            'qr_code' => 'valid_qr_code',
            'event_id' => $event->id
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }
}
