<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSection extends Model
{
    use HasFactory;

    public const HERO = 'hero';
    public const SERVICES = 'services';
    public const FAQ = 'faq';
    public const TRANSFERS = 'transfers';

    public const KEYS = [self::HERO, self::SERVICES, self::FAQ, self::TRANSFERS];

    protected $fillable = [
        'key',
        'title',
        'description',
        'image',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
