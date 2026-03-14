<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomTypeImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_url' => $this->image_path ? asset('storage/'.$this->image_path) : null,
            'alt_text' => $this->alt_text,
            'is_primary' => $this->is_primary,
        ];
    }
}
