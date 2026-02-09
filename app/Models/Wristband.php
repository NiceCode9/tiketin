<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Wristband extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'ticket_id',
        'status',
        'exchanged_at',
        'exchanged_by',
        'validated_at',
        'validated_by',
        'checksum',
    ];

    protected $casts = [
        'uuid' => 'string',
        'exchanged_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function booted(): void
    {
        // Auto-generate UUID and checksum
        static::creating(function ($wristband) {
            if (empty($wristband->uuid)) {
                $wristband->uuid = Str::uuid();
            }
            
            if (empty($wristband->checksum)) {
                $wristband->checksum = hash('sha256', $wristband->uuid . config('app.key'));
            }
        });
    }

    /**
     * A wristband belongs to a ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * A wristband was exchanged by a user (officer)
     */
    public function exchangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'exchanged_by');
    }

    /**
     * A wristband was validated by a user (validator)
     */
    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * A wristband has many scan logs
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
     * Check if wristband is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if wristband is validated
     */
    public function isValidated(): bool
    {
        return $this->status === 'validated';
    }

    /**
     * Check if wristband is revoked
     */
    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }
}
