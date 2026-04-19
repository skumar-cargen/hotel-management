<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number', 'domain_id', 'customer_id', 'hotel_id', 'room_type_id',
        'guest_first_name', 'guest_last_name', 'guest_email',
        'guest_phone', 'guest_nationality',
        'check_in_date', 'check_out_date', 'num_nights',
        'num_adults', 'num_children', 'num_rooms', 'special_requests',
        'room_price_per_night', 'subtotal', 'tax_amount', 'tax_percentage',
        'tourism_fee', 'service_charge', 'total_amount', 'currency',
        'status', 'cancellation_reason', 'cancelled_at', 'confirmed_at',
        'ip_address', 'user_agent', 'booked_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'room_price_per_night' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'tourism_fee' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'booked_at' => 'datetime',
            'status' => BookingStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function review(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getGuestFullNameAttribute(): string
    {
        return "{$this->guest_first_name} {$this->guest_last_name}";
    }

    public function scopePending($query)
    {
        return $query->where('status', BookingStatus::Pending);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', BookingStatus::Confirmed);
    }

    public function scopePaid($query)
    {
        return $query->where('status', BookingStatus::Paid);
    }
}
