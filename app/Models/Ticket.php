<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'order_id',
        'ticket_category_id',
        'seat_id',
        'consumer_name',
        'consumer_identity_type',
        'consumer_identity_number',
        'status',
        'checksum',
    ];

    protected $casts = [
        'uuid' => 'string',
    ];

    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        // Auto-generate UUID and checksum
        static::creating(function ($ticket) {
            if (empty($ticket->uuid)) {
                $ticket->uuid = Str::uuid();
            }
            
            if (empty($ticket->checksum)) {
                $ticket->checksum = hash('sha256', $ticket->uuid . config('app.key'));
            }
        });
    }

    /**
     * A ticket belongs to an order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * A ticket belongs to a ticket category
     */
    public function ticketCategory(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    /**
     * A ticket belongs to a seat (if seated)
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    /**
     * A ticket has one wristband
     */
    public function wristband(): HasOne
    {
        return $this->hasOne(Wristband::class);
    }

    /**
     * A ticket has many scan logs
     */
    public function scanLogs(): MorphMany
    {
        return $this->morphMany(ScanLog::class, 'scannable');
    }

    /**
     * Verify QR code checksum
     */
    public function verifyChecksum(string $checksum): bool
    {
        return hash_equals($this->checksum, $checksum);
    }

    /**
     * Check if ticket is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if ticket is exchanged
     */
    public function isExchanged(): bool
    {
        return $this->status === 'exchanged';
    }
}
