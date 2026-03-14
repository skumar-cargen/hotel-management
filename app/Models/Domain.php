<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'domain', 'slug', 'is_active', 'is_primary',
        'default_currency', 'default_language',
        'phone', 'email', 'address',
        'meta_title', 'meta_description', 'meta_keywords', 'og_image_path',
        'canonical_url', 'logo_path', 'favicon_path',
        'robots_txt', 'sitemap_enabled',
        'google_analytics_id', 'google_search_console_verification',
        'meta_pixel_id', 'google_tag_manager_id',
        'about_us', 'about_us_meta_title', 'about_us_meta_description', 'about_us_canonical_url',
        'privacy_policy', 'privacy_policy_meta_title', 'privacy_policy_meta_description', 'privacy_policy_canonical_url',
        'terms_conditions', 'terms_conditions_meta_title', 'terms_conditions_meta_description', 'terms_conditions_canonical_url',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
            'sitemap_enabled' => 'boolean',
        ];
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'domain_hotel')
            ->withPivot('is_active', 'sort_order');
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'domain_location')
            ->withPivot('is_active', 'sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(DomainAnalytics::class);
    }

    public function heroSlides(): HasMany
    {
        return $this->hasMany(DomainHeroSlide::class)->orderBy('sort_order');
    }

    public function deals(): BelongsToMany
    {
        return $this->belongsToMany(Deal::class, 'deal_domain');
    }

    public function careers(): BelongsToMany
    {
        return $this->belongsToMany(Career::class, 'career_domain');
    }

    public function contactInquiries(): HasMany
    {
        return $this->hasMany(ContactInquiry::class);
    }

    public function newsletterSubscribers(): HasMany
    {
        return $this->hasMany(NewsletterSubscriber::class);
    }

    public function blogPosts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_domain');
    }

    public function testimonials(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'domain_testimonials')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
