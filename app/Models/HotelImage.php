<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelImage extends Model
{
    const CATEGORIES = [
        'exterior' => 'Outside View',
        'lobby' => 'Lobby & Reception',
        'rooms' => 'Rooms',
        'bathroom' => 'Bathroom',
        'pool' => 'Pool & Beach',
        'restaurant' => 'Restaurant & Dining',
        'gym' => 'Gym & Fitness',
        'spa' => 'Spa & Wellness',
        'meeting' => 'Meeting & Events',
        'general' => 'General / Other',
    ];

    protected $fillable = [
        'hotel_id', 'category', 'image_path', 'thumbnail_path',
        'alt_text', 'caption', 'is_primary', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
