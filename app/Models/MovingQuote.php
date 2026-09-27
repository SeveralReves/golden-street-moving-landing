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
        'estimate_total',
        'estimate_range_low',
        'estimate_range_high',
        'estimate_hours',
        'estimate_breakdown',
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
        'estimate_total' => 'decimal:2',
        'estimate_range_low' => 'decimal:2',
        'estimate_range_high' => 'decimal:2',
        'estimate_hours' => 'decimal:2',
        'estimate_breakdown' => 'array',
    ];

    /**
     * Fields the public booking API must never return. The estimate is only
     * ever shown in the admin dashboard and the lead notification email.
     */
    public const PUBLIC_HIDDEN_FIELDS = [
        'estimate_total',
        'estimate_range_low',
        'estimate_range_high',
        'estimate_hours',
        'estimate_breakdown',
    ];
}
