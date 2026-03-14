<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingConfirmationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $summary = (new BookingSummaryResource($this->resource))->toArray($request);

        $summary['payments'] = $this->payments->map(fn ($payment) => [
            'transaction_id' => $payment->transaction_id,
            'payment_method' => $payment->payment_method,
            'amount' => (float) $payment->amount,
            'currency' => $payment->currency,
            'status' => $payment->status,
            'paid_at' => $payment->paid_at?->toIso8601String(),
        ]);

        $summary['confirmed_at'] = $this->confirmed_at?->toIso8601String();

        return $summary;
    }
}
