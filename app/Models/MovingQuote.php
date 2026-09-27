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
        'sms_consent',
        'origin_address',
        'origin_zip',
        'origin_lat',
        'origin_lng',
        'destination_address',
        'destination_zip',
        'destination_lat',
        'destination_lng',
        'preferred_date',
        'date_flexible',
        'schedule',
        'move_type',
        'bedrooms',
        'origin_floor',
        'origin_elevator',
        'destination_floor',
        'destination_elevator',
        'packing_service',
        'special_items',
        'photos',
        'comments',
        'status',
        'email_sent',
        'email_sent_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'date_flexible' => 'boolean',
        'sms_consent' => 'boolean',
        'origin_elevator' => 'boolean',
        'destination_elevator' => 'boolean',
        'packing_service' => 'boolean',
        'special_items' => 'array',
        'photos' => 'array',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
    ];
}
