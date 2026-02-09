<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\Order;
use App\Models\Event;
use App\Models\PromoCodeUsage;
use Illuminate\Support\Facades\DB;

class PromoService
{
    /**
     * Validate promo code for an event
     */
    public function validatePromoCode(string $code, Event $event): ?PromoCode
    {
        $promo = PromoCode::where('code', $code)
            ->where('event_id', $event->id)
            ->first();

        if (!$promo) {
            throw new \Exception('Invalid promo code');
        }

        if (!$promo->isValid()) {
            throw new \Exception('Promo code is not valid or has expired');
        }

        return $promo;
    }

    /**
     * Apply promo code to order
     */
    public function applyPromoCode(Order $order, PromoCode $promo): void
    {
        if (!$promo->meetsMinimumPurchase($order->subtotal)) {
            throw new \Exception("Minimum purchase amount of {$promo->min_purchase_amount} required");
        }

        // Check if quota is available (with pessimistic locking)
        DB::transaction(function () use ($order, $promo) {
            $promo = PromoCode::where('id', $promo->id)
                ->lockForUpdate()
                ->first();

            if ($promo->used_count >= $promo->quota) {
                throw new \Exception('Promo code quota has been reached');
            }

            $discountAmount = $promo->calculateDiscount($order->subtotal);

            // Update order
            $order->update([
                'discount_amount' => $discountAmount,
                'total_amount' => $order->subtotal - $discountAmount,
            ]);

            // Create usage record
            PromoCodeUsage::create([
                'promo_code_id' => $promo->id,
                'order_id' => $order->id,
                'discount_amount' => $discountAmount,
            ]);

            // Increment used count
            $promo->increment('used_count');

            // Auto-disable if quota reached
            if ($promo->used_count >= $promo->quota) {
                $promo->update(['status' => 'inactive']);
            }
        });
    }

    /**
     * Calculate discount without applying
     */
    public function calculateDiscount(string $code, Event $event, float $subtotal): array
    {
        try {
            $promo = $this->validatePromoCode($code, $event);
            
            if (!$promo->meetsMinimumPurchase($subtotal)) {
                return [
                    'valid' => false,
                    'message' => "Minimum purchase amount of {$promo->min_purchase_amount} required",
                    'discount_amount' => 0,
                ];
            }

            $discountAmount = $promo->calculateDiscount($subtotal);

            return [
                'valid' => true,
                'discount_amount' => $discountAmount,
                'final_amount' => $subtotal - $discountAmount,
                'promo_code' => $promo,
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => $e->getMessage(),
                'discount_amount' => 0,
            ];
        }
    }
}
