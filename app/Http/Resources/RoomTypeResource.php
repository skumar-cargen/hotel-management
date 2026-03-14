<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'max_guests' => $this->max_guests,
            'max_adults' => $this->max_adults,
            'max_children' => $this->max_children,
            'bed_type' => $this->bed_type,
            'room_size_sqm' => $this->room_size_sqm,
            'base_price' => (float) $this->base_price,
            'display_price' => $this->when(
                isset($this->display_price),
                fn () => $this->display_price
            ),
            'total_rooms' => $this->total_rooms,
            'amenities' => AmenityResource::collection($this->whenLoaded('amenities')),
            'images' => RoomTypeImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
