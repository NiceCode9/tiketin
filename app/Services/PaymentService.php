<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    protected string $serverKey;
    protected string $clientKey;
    protected string $baseUrl;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->clientKey = config('midtrans.client_key');
        $this->isProduction = config('midtrans.is_production', false);
        $this->baseUrl = $this->isProduction
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
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
                'finish' => route('payment.finish', ['order' => $order->order_token]),
            ],
        ];

        $response = Http::withBasicAuth($this->serverKey, '')
            ->post("{$this->baseUrl}/snap/v1/transactions", $params);

        if ($response->failed()) {
            throw new \Exception('Failed to create payment token: ' . $response->body());
        }

        return $response->json('token');
    }

    /**
     * Handle payment notification callback from Midtrans
     */
    public function handleCallback(array $payload): void
    {
        // Verify signature
        if (!$this->verifySignature($payload)) {
            throw new \Exception('Invalid signature');
        }

        $orderNumber = $payload['order_id'];
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'] ?? null;

        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        DB::transaction(function () use ($order, $payload, $transactionStatus, $fraudStatus) {
            // Create or update payment transaction
            PaymentTransaction::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'transaction_id' => $payload['transaction_id'],
                ],
                [
                    'payment_type' => $payload['payment_type'] ?? null,
                    'gross_amount' => $payload['gross_amount'] ?? $order->total_amount,
                    'status' => $this->mapTransactionStatus($transactionStatus),
                    'raw_response' => $payload,
                ]
            );

            // Update order status based on transaction status
            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $this->processSuccessfulPayment($order, $payload);
                }
            } elseif ($transactionStatus === 'settlement') {
                $this->processSuccessfulPayment($order, $payload);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $this->processFailedPayment($order, $transactionStatus);
            } elseif ($transactionStatus === 'pending') {
                // Payment is pending, no action needed
                $order->update(['payment_status' => 'pending']);
            }
        });
    }

    /**
     * Verify Midtrans signature
     */
    public function verifySignature(array $payload): bool
    {
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $serverKey = $this->serverKey;

        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return $signatureKey === $payload['signature_key'];
    }

    /**
     * Get order status from Midtrans
     */
    public function getOrderStatus(string $orderNumber): array
    {
        $response = Http::withBasicAuth($this->serverKey, '')
            ->get("{$this->baseUrl}/v2/{$orderNumber}/status");

        if ($response->failed()) {
            throw new \Exception('Failed to get order status');
        }

        return $response->json();
    }

    /**
     * Process successful payment
     */
    protected function processSuccessfulPayment(Order $order, array $paymentData): void
    {
        if ($order->isPaid()) {
            // Already processed
            return;
        }

        $orderService = app(OrderService::class);
        $orderService->markAsPaid($order, $paymentData);

        // TODO: Send payment success notification
    }

    /**
     * Process failed payment
     */
    protected function processFailedPayment(Order $order, string $status): void
    {
        $order->update(['payment_status' => $status]);

        // Release seats and quota
        $orderService = app(OrderService::class);
        // This would be handled by the expiration job
    }

    /**
     * Map Midtrans transaction status to our status
     */
    protected function mapTransactionStatus(string $status): string
    {
        return match ($status) {
            'capture', 'settlement' => 'settlement',
            'pending' => 'pending',
            'deny' => 'deny',
            'cancel' => 'cancel',
            'expire' => 'expire',
            default => 'pending',
        };
    }

    /**
     * Get item details for Midtrans
     */
    protected function getItemDetails(Order $order): array
    {
        $items = [];

        foreach ($order->orderItems as $item) {
            $items[] = [
                'id' => $item->ticket_category_id,
                'price' => (int) $item->unit_price,
                'quantity' => $item->quantity,
                'name' => $item->ticketCategory->name,
            ];
        }

        // Add discount as negative item if applicable
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
