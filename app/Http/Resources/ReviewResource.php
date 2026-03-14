<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'guest_name' => $this->guest_name,
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'is_verified' => $this->is_verified,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
