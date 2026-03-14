<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'location_id', 'star_rating',
        'description', 'short_description',
        'address', 'latitude', 'longitude',
        'phone', 'email', 'website',
        'check_in_time', 'check_out_time', 'cancellation_policy',
        'is_beach_access', 'is_family_friendly',
        'is_active', 'is_featured',
        'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
        'faq_data', 'sort_order',
        'avg_rating', 'total_reviews', 'min_price',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_beach_access' => 'boolean',
            'is_family_friendly' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'faq_data' => 'array',
            'avg_rating' => 'decimal:2',
            'min_price' => 'decimal:2',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(HotelImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(HotelImage::class)->where('is_primary', true);
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenity');
    }

    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'domain_hotel')
            ->withPivot('is_active', 'sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function deals(): BelongsToMany
    {
        return $this->belongsToMany(Deal::class, 'deal_hotel');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForDomain($query, $domainId)
    {
        return $query->whereHas('domains', fn ($q) => $q->where('domains.id', $domainId)->where('domain_hotel.is_active', true));
    }
}
