<?php

namespace App\Models;

use App\Models\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'order_token',
        'event_id',
        'consumer_name',
        'consumer_city',
        'consumer_birth_date',
        'consumer_email',
        'consumer_whatsapp',
        'consumer_identity_type',
        'consumer_identity_number',
        'subtotal',
        'discount_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'paid_at',
        'expires_at',
        'invoice_path',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope);

        // Auto-generate order number and token
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));
            }

            if (empty($order->order_token)) {
                $order->order_token = Str::uuid();
            }
        });
    }

    /**
     * An order belongs to an event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * An order has many order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * An order has many tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * An order has many payment transactions
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * An order has many promo code usages
     */
    public function promoCodeUsages(): HasMany
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    /**
     * Check if order is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'success' || $this->payment_status === 'paid';
    }

    /**
     * Check if order is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    /**
     * Get a virtual customer object for compatibility with frontend examples
     */
    public function getCustomerAttribute(): object
    {
        return (object) [
            'full_name' => $this->consumer_name,
            'email' => $this->consumer_email,
            'phone_number' => $this->consumer_whatsapp,
        ];
    }

    /**
     * Get status badge info
     */
    public function getStatusBadge(): array
    {
        return match ($this->payment_status) {
            'paid', 'success' => [
                'label' => 'Sudah Dibayar',
                'color' => 'success',
            ],
            'pending' => [
                'label' => 'Menunggu Pembayaran',
                'color' => 'warning',
            ],
            'expired' => [
                'label' => 'Kadaluarsa',
                'color' => 'danger',
            ],
            'failed' => [
                'label' => 'Gagal',
                'color' => 'danger',
            ],
            default => [
                'label' => 'Unknown',
                'color' => 'info',
            ],
        };
    }
}
