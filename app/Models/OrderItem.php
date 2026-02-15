<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'ticket_category_id',
        'seat_id',
        'quantity',
        'unit_price',
        'biaya_layanan',
        'biaya_admin_payment',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'biaya_layanan' => 'decimal:2',
        'biaya_admin_payment' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * An order item belongs to an order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * An order item belongs to a ticket category
     */
    public function ticketCategory(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    /**
     * An order item belongs to a seat (if seated)
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'success' || $this->payment_status === 'paid';
    }

    /**
     * Get the first promo code usage for this order
     */
    public function getPromoUsageAttribute()
    {
        return $this->promoCodeUsages()->first();
    }

    /**
     * Compatibility accessor for ticket type
     */
    public function getTicketTypeAttribute()
    {
        return $this->ticketCategory;
    }
}
