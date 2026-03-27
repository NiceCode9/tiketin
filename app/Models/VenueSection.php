<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VenueSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'name',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    /**
     * Boot the model and add hooks for capacity sync
     */
    protected static function booted()
    {
        static::saved(function ($section) {
            $section->venue->updateTotalCapacity();
        });

        static::deleted(function ($section) {
            $section->venue->updateTotalCapacity();
        });
    }

    /**
     * A section belongs to a venue
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * A section has many seats
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * A section has many ticket categories
     */
    public function ticketCategories(): HasMany
    {
        return $this->hasMany(TicketCategory::class);
    }

    /**
     * Synchronize the capacity field with the actual count of seats
     */
    public function syncCapacityWithSeats(): void
    {
        $count = $this->seats()->count();
        $this->update(['capacity' => $count]);
    }
}
