<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class PaymentService
{
    public function __construct()
    {
        $this->configureMidtrans();
    }

    /**
     * Configure Midtrans settings
     */
    protected function configureMidtrans(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create Midtrans Snap token for payment
     */
    public function createSnapToken(Order $order): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->consumer_name,
                'email' => $order->consumer_email,
                'phone' => $order->consumer_whatsapp,
            ],
            'item_details' => $this->getItemDetails($order),
            'callbacks' => [
                'finish' => route('payment.finish', ['orderToken' => $order->order_token]),
            ],
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create payment token: '.$e->getMessage());
        }
    }

    /**
     * Handle payment notification callback from Midtrans
     */
    public function handleCallback(?array $payload = null): void
    {
        // $payload is handled automatically by Midtrans\Notification if not passed,
        // but we can pass it if we retrieved it from Request

        try {
            $notif = new Notification;
        } catch (\Exception $e) {
            throw new \Exception('Invalid Midtrans Notification');
        }

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        $order = Order::where('order_number', $orderId)->firstOrFail();

        DB::transaction(function () use ($order, $notif, $transaction, $type, $fraud) {
            // Create or update payment transaction
            PaymentTransaction::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'transaction_id' => $notif->transaction_id,
                ],
                [
                    'payment_type' => $type,
                    'gross_amount' => $notif->gross_amount,
                    'status' => $transaction,
                    'raw_response' => (array) $notif->getResponse(),
                ]
            );

            if ($transaction == 'capture') {
                if ($fraud == 'challenge') {
                    // TODO: Set payment status in merchant's database to 'challenge'
                    // For now we don't handle challenge automatically
                } elseif ($fraud == 'accept') {
                    $this->processSuccessfulPayment($order, (array) $notif->getResponse());
                }
            } elseif ($transaction == 'settlement') {
                $this->processSuccessfulPayment($order, (array) $notif->getResponse());
            } elseif ($transaction == 'pending') {
                $order->update(['payment_status' => 'pending']);
            } elseif ($transaction == 'deny') {
                $this->processFailedPayment($order, 'failed');
            } elseif ($transaction == 'expire') {
                $this->processFailedPayment($order, 'expired');
            } elseif ($transaction == 'cancel') {
                $this->processFailedPayment($order, 'canceled');
            }
        });
    }

    /**
     * Process successful payment
     */
    protected function processSuccessfulPayment(Order $order, array $paymentData): void
    {
        if ($order->isPaid()) {
            return;
        }

        $orderService = app(OrderService::class);
        $orderService->markAsPaid($order, $paymentData);
    }

    /**
     * Process failed payment
     */
    protected function processFailedPayment(Order $order, string $status): void
    {
        $order->update(['payment_status' => $status]);
    }

    /**
     * Get item details for Midtrans
     */
    protected function getItemDetails(Order $order): array
    {
        $items = [];

        foreach ($order->orderItems as $item) {
            $items[] = [
                'id' => (string) $item->ticket_category_id,
                'price' => (int) $item->unit_price,
                'quantity' => $item->quantity,
                'name' => substr($item->ticketCategory->name, 0, 50), // Midtrans limit
            ];
        }

        if ($order->discount_amount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'price' => -(int) $order->discount_amount,
                'quantity' => 1,
                'name' => 'Promo Code Discount',
            ];
        }

        return $items;
    }
}
