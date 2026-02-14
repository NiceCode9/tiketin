<?php

namespace App\Models;

use App\Models\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromoCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'code',
        'discount_type',
        'discount_value',
        'quota',
        'used_count',
        'min_purchase_amount',
        'max_discount_amount',
        'valid_from',
        'valid_until',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'quota' => 'integer',
        'used_count' => 'integer',
        'min_purchase_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    /**
     * Boot the model and apply global scope for client isolation
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope);
    }

    /**
     * A promo code belongs to an event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * A promo code has many usages
     */
    public function usages(): HasMany
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    /**
     * Check if promo code is valid
     */
    public function isValid(): bool
    {
        $now = now();

        return $this->status === 'active'
            && $now->between($this->valid_from, $this->valid_until)
            && $this->used_count < $this->quota;
    }

    /**
     * Check if promo code meets minimum purchase requirement
     */
    public function meetsMinimumPurchase(float $amount): bool
    {
        if (! $this->min_purchase_amount) {
            return true;
        }

        return $amount >= $this->min_purchase_amount;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $subtotal): float
    {
        // Percentage discount
        $discount = $subtotal * ($this->discount_value / 100);

        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $subtotal);
        }
        // Apply max discount cap if set
        // if ($this->max_discount_amount) {
        //     $discount = min($discount, $this->max_discount_amount);
        // }

        return round($discount, 2);
    }
}
