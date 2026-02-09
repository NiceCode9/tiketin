<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'capacity',
        'has_seating',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'has_seating' => 'boolean',
    ];

    /**
     * A venue has many sections
     */
    public function sections(): HasMany
    {
        return $this->hasMany(VenueSection::class);
    }

    /**
     * A venue has many events
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
