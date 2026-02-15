<?php

namespace App\Models;

use App\Models\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'venue_section_id',
        'name',
        'price',
        'biaya_layanan',
        'biaya_admin_payment',
        'quota',
        'sold_count',
        'is_seated',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'biaya_layanan' => 'decimal:2',
        'biaya_admin_payment' => 'decimal:2',
        'quota' => 'integer',
        'sold_count' => 'integer',
        'is_seated' => 'boolean',
    ];

    /**
     * Boot the model and apply global scope for client isolation
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope);
    }

    /**
     * A ticket category belongs to an event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * A ticket category belongs to a venue section (if seated)
     */
    public function venueSection(): BelongsTo
    {
        return $this->belongsTo(VenueSection::class);
    }

    /**
     * A ticket category has many tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * A ticket category has many order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get available tickets count
     */
    public function getAvailableCountAttribute(): int
    {
        return max(0, $this->quota - $this->sold_count);
    }

    /**
     * Check if tickets are available
     */
    public function hasAvailableTickets(int $quantity = 1): bool
    {
        return $this->available_count >= $quantity;
    }
}
