<?php

namespace App\Models;

use App\Models\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'address',
        'city',
        'capacity',
        'has_seating',
        'image',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'has_seating' => 'boolean',
    ];

    /**
     * Boot the model and apply global scope for client isolation
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope);
    }

    /**
     * A venue belongs to a client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

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
