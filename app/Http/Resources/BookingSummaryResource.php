<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'reference_number' => $this->reference_number,
            'status' => $this->status,
            'hotel' => [
                'name' => $this->hotel->name,
                'slug' => $this->hotel->slug,
                'star_rating' => $this->hotel->star_rating,
                'address' => $this->hotel->address,
            ],
            'room_type' => [
                'name' => $this->roomType->name,
                'slug' => $this->roomType->slug,
            ],
            'dates' => [
                'check_in' => $this->check_in_date->format('Y-m-d'),
                'check_out' => $this->check_out_date->format('Y-m-d'),
                'num_nights' => $this->num_nights,
            ],
            'guests' => [
                'num_adults' => $this->num_adults,
                'num_children' => $this->num_children,
                'num_rooms' => $this->num_rooms,
            ],
            'pricing' => [
                'room_price_per_night' => (float) $this->room_price_per_night,
                'subtotal' => (float) $this->subtotal,
                'tax_percentage' => (float) $this->tax_percentage,
                'tax_amount' => (float) $this->tax_amount,
                'tourism_fee' => (float) $this->tourism_fee,
                'service_charge' => (float) $this->service_charge,
                'total_amount' => (float) $this->total_amount,
                'currency' => $this->currency,
            ],
            'guest_info' => [
                'first_name' => $this->guest_first_name,
                'last_name' => $this->guest_last_name,
                'email' => $this->guest_email,
                'phone' => $this->guest_phone,
                'nationality' => $this->guest_nationality,
            ],
            'special_requests' => $this->special_requests,
            'booked_at' => $this->booked_at?->toIso8601String(),
        ];
    }
}
