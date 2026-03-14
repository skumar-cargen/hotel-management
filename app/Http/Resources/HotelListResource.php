<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $primaryImage = $this->relationLoaded('images')
            ? $this->images->firstWhere('is_primary', true) ?? $this->images->first()
            : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'star_rating' => $this->star_rating,
            'short_description' => $this->short_description,
            'address' => $this->address,
            'avg_rating' => (float) $this->avg_rating,
            'total_reviews' => $this->total_reviews,
            'min_price' => (float) ($this->display_min_price ?? $this->min_price),
            'is_beach_access' => $this->is_beach_access,
            'is_family_friendly' => $this->is_family_friendly,
            'primary_image' => $primaryImage ? [
                'image_url' => asset('storage/'.$primaryImage->image_path),
                'alt_text' => $primaryImage->alt_text,
            ] : null,
            'location' => $this->whenLoaded('location', fn () => [
                'name' => $this->location->name,
                'slug' => $this->location->slug,
                'city' => $this->location->city,
            ]),
            'deals' => $this->whenLoaded('deals', fn () => $this->deals->map(fn ($deal) => [
                'title' => $deal->title,
                'slug' => $deal->slug,
                'discount_type' => $deal->discount_type,
                'discount_value' => (float) $deal->discount_value,
                'end_date' => $deal->end_date->toDateString(),
            ])),
        ];
    }
}
