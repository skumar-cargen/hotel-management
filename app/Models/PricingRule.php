<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PricingRule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'domain_id', 'hotel_id', 'room_type_id', 'location_id',
        'adjustment_type', 'adjustment_value',
        'start_date', 'end_date', 'days_of_week',
        'priority', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'adjustment_value' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'days_of_week' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
