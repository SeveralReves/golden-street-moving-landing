<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'unit',
        'label',
        'note',
        'group',
        'confirmed',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'confirmed' => 'boolean',
    ];
}
