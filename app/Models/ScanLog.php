<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ScanLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'scanned_by',
        'event_id',
        'scannable_type',
        'scannable_id',
        'scan_type',
        'status',
        'error_message',
        'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    /**
     * A scan log belongs to a user (scanner)
     */
    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    /**
     * A scan log belongs to an event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * A scan log belongs to a scannable (Ticket or Wristband)
     */
    public function scannable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if scan was successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }
}
