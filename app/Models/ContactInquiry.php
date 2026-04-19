<?php

namespace App\Models;

use App\Enums\ContactInquiryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactInquiry extends Model
{
    protected $fillable = [
        'domain_id', 'hotel_id', 'name', 'email',
        'phone', 'subject', 'message', 'status', 'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContactInquiryStatus::class,
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
}
