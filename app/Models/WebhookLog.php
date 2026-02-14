<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'order_id',
        'type',
        'payload',
        'status',
        'response',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
