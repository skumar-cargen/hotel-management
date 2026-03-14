<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerApplication extends Model
{
    protected $fillable = [
        'career_id', 'domain_id', 'name', 'email',
        'phone', 'cover_letter', 'resume_path', 'status',
    ];

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
