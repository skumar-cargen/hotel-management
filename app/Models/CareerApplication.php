<?php

namespace App\Models;

use App\Enums\CareerApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerApplication extends Model
{
    protected $fillable = [
        'career_id', 'domain_id', 'name', 'email',
        'phone', 'cover_letter', 'resume_path', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => CareerApplicationStatus::class,
        ];
    }

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
