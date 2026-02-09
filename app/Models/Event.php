<?php

namespace App\Models;

use App\Models\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'venue_id',
        'name',
        'slug',
        'description',
        'event_date',
        'event_end_date',
        'status',
        'has_assigned_seating',
        'wristband_exchange_start',
        'wristband_exchange_end',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'event_end_date' => 'datetime',
        'has_assigned_seating' => 'boolean',
        'wristband_exchange_start' => 'datetime',
        'wristband_exchange_end' => 'datetime',
    ];

    /**
     * Boot the model and apply global scope for client isolation
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope);
        
        // Auto-generate slug from name
        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->name);
            }
        });
    }

    /**
     * An event belongs to a client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * An event belongs to a venue
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * An event has many ticket categories
     */
    public function ticketCategories(): HasMany
    {
        return $this->hasMany(TicketCategory::class);
    }

    /**
     * An event has many tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class)->through('orders');
    }

    /**
     * An event has many orders
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * An event has many promo codes
     */
    public function promoCodes(): HasMany
    {
        return $this->hasMany(PromoCode::class);
    }

    /**
     * An event has many scan logs
     */
    public function scanLogs(): HasMany
    {
        return $this->hasMany(ScanLog::class);
    }

    /**
     * Check if wristband exchange is currently active
     */
    public function isWristbandExchangeActive(): bool
    {
        if (!$this->wristband_exchange_start || !$this->wristband_exchange_end) {
            return false;
        }

        $now = now();
        return $now->between($this->wristband_exchange_start, $this->wristband_exchange_end);
    }
}
