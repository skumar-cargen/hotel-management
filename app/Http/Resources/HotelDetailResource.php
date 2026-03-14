<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'star_rating' => $this->star_rating,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'cancellation_policy' => $this->cancellation_policy,
            'is_beach_access' => $this->is_beach_access,
            'is_family_friendly' => $this->is_family_friendly,
            'avg_rating' => (float) $this->avg_rating,
            'total_reviews' => $this->total_reviews,
            'min_price' => (float) $this->min_price,
            'faq_data' => $this->faq_data,
            'images' => HotelImageResource::collection($this->whenLoaded('images')),
            'amenities' => AmenityResource::collection($this->whenLoaded('amenities')),
            'room_types' => RoomTypeResource::collection($this->whenLoaded('roomTypes')),
            'location' => $this->whenLoaded('location', fn () => new LocationResource($this->location)),
            'reviews_summary' => [
                'avg_rating' => (float) $this->avg_rating,
                'total_reviews' => $this->total_reviews,
                'recent_reviews' => ReviewResource::collection(
                    $this->whenLoaded('approvedReviews', fn () => $this->approvedReviews->take(5))
                ),
            ],
            'deals' => DealResource::collection($this->whenLoaded('deals')),
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'keywords' => $this->meta_keywords,
            ],
        ];
    }
}
