<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'transaction_id', 'payment_method', 'gateway',
        'amount', 'currency', 'status',
        'gateway_response', 'refund_amount', 'refund_transaction_id',
        'refunded_at', 'paid_at', 'failed_at', 'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'gateway_response' => 'array',
            'refunded_at' => 'datetime',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
