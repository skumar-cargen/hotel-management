<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'thumbnail_url' => $this->thumbnail_path ? asset('storage/'.$this->thumbnail_path) : null,
            'alt_text' => $this->alt_text,
            'caption' => $this->caption,
            'is_primary' => $this->is_primary,
        ];
    }
}
