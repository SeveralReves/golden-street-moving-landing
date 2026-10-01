<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoveEvent extends Model
{
    use HasFactory;

    public const STATUSES = ['scheduled', 'in_progress', 'completed', 'cancelled'];

    /** Statuses that occupy a crew and therefore count towards capacity. */
    public const ACTIVE_STATUSES = ['scheduled', 'in_progress'];

    protected $fillable = [
        'moving_quote_id',
        'title',
        'customer_name',
        'customer_phone',
        'origin_address',
        'destination_address',
        'start_at',
        'end_at',
        'crew_size',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'crew_size' => 'integer',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(MovingQuote::class, 'moving_quote_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    public function scopeOverlapping(Builder $query, $start, $end): Builder
    {
        return $query->where('start_at', '<', $end)->where('end_at', '>', $start);
    }
}
