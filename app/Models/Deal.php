<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Deal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description',
        'discount_type', 'discount_value',
        'start_date', 'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Deal $deal) {
            if (empty($deal->slug)) {
                $deal->slug = Str::slug($deal->title);
            }
        });

        static::updating(function (Deal $deal) {
            if ($deal->isDirty('title') && ! $deal->isDirty('slug')) {
                $deal->slug = Str::slug($deal->title);
            }
        });
    }

    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'deal_domain');
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'deal_hotel');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString());
    }
}
