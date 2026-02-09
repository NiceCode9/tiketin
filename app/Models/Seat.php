<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_section_id',
        'row_label',
        'seat_number',
        'status',
    ];

    protected $casts = [
        'seat_number' => 'integer',
        'status' => 'string',
    ];

    /**
     * A seat belongs to a venue section
     */
    public function venueSection(): BelongsTo
    {
        return $this->belongsTo(VenueSection::class);
    }

    /**
     * A seat has many tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * A seat has many order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the full seat identifier (e.g., "A-12")
     */
    public function getFullSeatAttribute(): string
    {
        return "{$this->row_label}-{$this->seat_number}";
    }
}
