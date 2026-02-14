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
        try {
            $notif = new Notification;
            $data = (array) $notif->getResponse();

            // 1. Manual Signature Verification (Security Recommendation)
            $serverKey = config('midtrans.server_key');
            $orderId = $notif->order_id;
            $statusCode = $notif->status_code;
            $grossAmount = $notif->gross_amount;
            
            $input = $orderId . $statusCode . $grossAmount . $serverKey;
            $signature = hash('sha512', $input);

            if ($signature !== $notif->signature_key) {
                \Illuminate\Support\Facades\Log::warning('Midtrans: Invalid Signature Detected', [
                    'order_id' => $orderId,
                    'status_code' => $statusCode,
                    'gross_amount' => $grossAmount,
                ]);
                throw new \Exception('Invalid Midtrans Signature');
            }

            // 2. Logging for Debugging/Auditing
            \Illuminate\Support\Facades\Log::info('Midtrans: Processing Notification', [
                'order_id' => $orderId,
                'status' => $notif->transaction_status,
                'payment_type' => $notif->payment_type,
            ]);

            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $fraud = $notif->fraud_status;

            $order = Order::where('order_number', $orderId)->first();

            if (!$order) {
                \Illuminate\Support\Facades\Log::error('Midtrans: Order Not Found', ['order_id' => $orderId]);
                return;
            }

            DB::transaction(function () use ($order, $notif, $transaction, $type, $fraud, $data) {
                // Create or update payment transaction record
                PaymentTransaction::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'transaction_id' => $notif->transaction_id,
                    ],
                    [
                        'payment_type' => $type,
                        'gross_amount' => $notif->gross_amount,
                        'status' => $transaction,
                        'raw_response' => $data,
                    ]
                );

                // 3. Status Mapping logic
                if ($transaction == 'capture') {
                    if ($fraud == 'challenge') {
                        $order->update(['payment_status' => 'challenge']);
                    } elseif ($fraud == 'accept') {
                        $this->processSuccessfulPayment($order, $data);
                    }
                } elseif ($transaction == 'settlement') {
                    $this->processSuccessfulPayment($order, $data);
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

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans: Callback Processing Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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
