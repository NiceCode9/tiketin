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
    ];

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
}
