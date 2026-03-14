<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'reference_number' => $this->reference_number,
            'status' => $this->status,
            'hotel' => [
                'name' => $this->hotel?->name,
                'slug' => $this->hotel?->slug,
                'star_rating' => $this->hotel?->star_rating,
                'address' => $this->hotel?->address,
            ],
            'room_type' => [
                'name' => $this->roomType?->name,
                'slug' => $this->roomType?->slug,
            ],
            'check_in_date' => $this->check_in_date?->toDateString(),
            'check_out_date' => $this->check_out_date?->toDateString(),
            'num_nights' => $this->num_nights,
            'num_adults' => $this->num_adults,
            'num_children' => $this->num_children,
            'num_rooms' => $this->num_rooms,
            'room_price_per_night' => $this->room_price_per_night,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'tax_percentage' => $this->tax_percentage,
            'tourism_fee' => $this->tourism_fee,
            'service_charge' => $this->service_charge,
            'total_amount' => $this->total_amount,
            'currency' => $this->currency,
            'special_requests' => $this->special_requests,
            'booked_at' => $this->booked_at?->toIso8601String(),
            'confirmed_at' => $this->confirmed_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
        ];
    }
}
