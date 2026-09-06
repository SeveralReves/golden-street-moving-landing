<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovingQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'origin_address',
        'origin_lat',
        'origin_lng',
        'destination_address',
        'destination_lat',
        'destination_lng',
        'preferred_date',
        'schedule',
        'move_type',
        'origin_floor',
        'origin_elevator',
        'destination_floor',
        'destination_elevator',
        'packing_service',
        'comments',
        'status',
        'email_sent',
        'email_sent_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'origin_elevator' => 'boolean',
        'destination_elevator' => 'boolean',
        'packing_service' => 'boolean',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
    ];
}
