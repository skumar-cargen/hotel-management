<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAvailability extends Model
{
    protected $table = 'room_availability';

    protected $fillable = [
        'room_type_id', 'date', 'available_rooms', 'booked_rooms',
        'price_override', 'is_closed',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'price_override' => 'decimal:2',
            'is_closed' => 'boolean',
        ];
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }
}
