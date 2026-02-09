<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * A client has many users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * A client has many events
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
