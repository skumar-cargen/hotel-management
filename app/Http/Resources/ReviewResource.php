<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $ip = $request->ip();

        return [
            'id' => $this->id,
            'guest_name' => $this->guest_name,
            'rating' => $this->rating,
            'title' => $this->title,
            'comment' => $this->comment,
            'is_verified' => $this->is_verified,
            'helpful_count' => $this->helpfuls_count ?? $this->helpfuls()->count(),
            'is_helpful' => $this->relationLoaded('helpfuls')
                ? $this->helpfuls->contains('ip_address', $ip)
                : $this->helpfuls()->where('ip_address', $ip)->exists(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
