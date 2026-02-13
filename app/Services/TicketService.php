<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Seat;
use App\Models\Ticket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * Generate tickets for a paid order
     */
    public function generateTickets(Order $order): Collection
    {
        if (! $order->isPaid()) {
            throw new \Exception('Order must be paid before generating tickets');
        }

        return DB::transaction(function () use ($order) {
            $tickets = collect();

            foreach ($order->orderItems as $item) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    $ticket = Ticket::create([
                        'order_id' => $order->id,
                        'ticket_category_id' => $item->ticket_category_id,
                        'seat_id' => $item->seat_id,
                        'consumer_name' => $order->consumer_name,
                        'consumer_identity_type' => $order->consumer_identity_type,
                        'consumer_identity_number' => $order->consumer_identity_number,
                        'status' => 'paid',
                    ]);

                    $tickets->push($ticket);
                }
            }

            return $tickets;
        });
    }

    /**
     * Validate ticket QR code
     */
    public function validateTicketQR(string $uuid, string $checksum): Ticket
    {
        $ticket = Ticket::where('uuid', $uuid)->firstOrFail();

        if (! $ticket->verifyChecksum($checksum)) {
            throw new \Exception('Invalid QR code checksum');
        }

        return $ticket;
    }

    /**
     * Check if ticket can be exchanged for wristband
     */
    public function canExchangeForWristband(Ticket $ticket): bool
    {
        // Must be paid
        if (! $ticket->isPaid()) {
            return false;
        }

        // Must not be already exchanged
        if ($ticket->isExchanged()) {
            return false;
        }

        // Check if wristband exchange window is active
        $event = $ticket->order->event;
        if (! $event->isWristbandExchangeActive()) {
            return false;
        }

        return true;
    }

    /**
     * Get ticket QR code data
     */
    public function getQRCodeData(Ticket $ticket): array
    {
        return [
            'type' => 'ticket',
            'id' => $ticket->uuid,
            'checksum' => $ticket->checksum,
        ];
    }

    /**
     * Assign seat to ticket
     */
    public function assignSeat(Ticket $ticket, Seat $seat): void
    {
        if ($seat->status !== 'available') {
            throw new \Exception('Seat is not available');
        }

        DB::transaction(function () use ($ticket, $seat) {
            $seat->update(['status' => 'reserved']);
            $ticket->update(['seat_id' => $seat->id]);
        });
    }
}
