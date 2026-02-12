<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Seat;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create a new order with items
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Create the order
            $order = Order::create([
                'event_id' => $data['event_id'],
                'consumer_name' => $data['consumer_name'],
                'consumer_email' => $data['consumer_email'],
                'consumer_whatsapp' => $data['consumer_whatsapp'],
                'consumer_identity_type' => $data['consumer_identity_type'],
                'consumer_identity_number' => $data['consumer_identity_number'],
                'subtotal' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'payment_status' => 'pending',
                'expires_at' => now()->addMinutes(config('ticketing.order_expiration_minutes', 30)),
            ]);

            $subtotal = 0;

            // Create order items
            foreach ($data['items'] as $item) {
                $ticketCategory = TicketCategory::findOrFail($item['ticket_category_id']);

                // Check availability
                if (! $ticketCategory->hasAvailableTickets($item['quantity'])) {
                    throw new \Exception("Insufficient tickets available for {$ticketCategory->name}");
                }

                // Handle seat assignment if seated
                $seatId = null;
                if ($ticketCategory->is_seated && isset($item['seat_id'])) {
                    $seat = Seat::findOrFail($item['seat_id']);

                    // Verify seat is available
                    if ($seat->status !== 'available') {
                        throw new \Exception("Seat {$seat->full_seat} is not available");
                    }

                    // Reserve the seat temporarily
                    $seat->update(['status' => 'reserved']);
                    $seatId = $seat->id;
                }

                $itemSubtotal = $ticketCategory->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_category_id' => $ticketCategory->id,
                    'seat_id' => $seatId,
                    'quantity' => $item['quantity'],
                    'unit_price' => $ticketCategory->price,
                    'subtotal' => $itemSubtotal,
                ]);

                // Increment sold count
                $ticketCategory->increment('sold_count', $item['quantity']);
            }

            // Update order totals
            $order->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
            ]);

            return $order->fresh(['orderItems']);
        });
    }

    /**
     * Calculate order total with promo code
     */
    public function calculateTotal(Order $order, ?string $promoCode = null): array
    {
        $subtotal = $order->subtotal;
        $discountAmount = 0;

        if ($promoCode) {
            $promoService = app(PromoService::class);
            $promo = $promoService->validatePromoCode($promoCode, $order->event);

            if ($promo && $promo->meetsMinimumPurchase($subtotal)) {
                $discountAmount = $promo->calculateDiscount($subtotal);
            }
        }

        $total = $subtotal - $discountAmount;

        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'total_amount' => max(0, $total),
        ];
    }

    /**
     * Cancel expired orders
     */
    public function cancelExpiredOrders(): int
    {
        $expiredOrders = Order::where('payment_status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                // Release reserved seats
                foreach ($order->orderItems as $item) {
                    if ($item->seat_id) {
                        Seat::where('id', $item->seat_id)
                            ->update(['status' => 'available']);
                    }

                    // Decrement sold count
                    $item->ticketCategory->decrement('sold_count', $item->quantity);
                }

                // Update order status
                $order->update(['payment_status' => 'expired']);
            });

            $count++;
        }

        return $count;
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(Order $order, array $paymentData): void
    {
        DB::transaction(function () use ($order, $paymentData) {
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => $paymentData['payment_method'] ?? null,
                'paid_at' => now(),
            ]);

            // Convert reserved seats to permanent
            foreach ($order->orderItems as $item) {
                if ($item->seat_id) {
                    // Seat remains reserved until ticket is used
                    // No action needed here
                }
            }

            // Generate tickets
            $ticketService = app(TicketService::class);
            $ticketService->generateTickets($order);
        });
    }
}
