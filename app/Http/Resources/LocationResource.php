<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'city' => $this->city,
            'country' => $this->country,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_featured' => $this->is_featured,
            'hotel_count' => $this->when(isset($this->hotels_count), $this->hotels_count),
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'keywords' => $this->meta_keywords,
            ],
        ];
    }
}
