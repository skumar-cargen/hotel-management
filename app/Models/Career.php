<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Career extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'location', 'job_type', 'department',
        'about_role', 'responsibilities', 'requirements', 'what_we_offer',
        'last_apply_date', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'last_apply_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Career $career) {
            if (empty($career->slug)) {
                $career->slug = Str::slug($career->title);
            }
        });

        static::updating(function (Career $career) {
            if ($career->isDirty('title') && ! $career->isDirty('slug')) {
                $career->slug = Str::slug($career->title);
            }
        });
    }

    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'career_domain');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(CareerApplication::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('last_apply_date', '>=', now()->toDateString());
    }
}
