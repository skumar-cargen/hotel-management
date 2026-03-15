<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'hotel_id', 'booking_id', 'guest_name', 'guest_email',
        'rating', 'title', 'comment',
        'is_verified', 'is_approved', 'admin_reply', 'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_approved' => 'boolean',
            'replied_at' => 'datetime',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function testimonialDomains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'domain_testimonials')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function helpfuls(): HasMany
    {
        return $this->hasMany(ReviewHelpful::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
