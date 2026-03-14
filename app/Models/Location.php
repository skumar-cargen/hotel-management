<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'city', 'country',
        'description', 'short_description',
        'latitude', 'longitude', 'image_path',
        'meta_title', 'meta_description', 'meta_keywords', 'seo_content', 'canonical_url',
        'sort_order', 'is_active', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'domain_location')
            ->withPivot('is_active', 'sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
