<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DomainHeroSlideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'image' => asset('storage/'.$this->image_path),
            'title' => $this->title,
            'highlight' => $this->highlight,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
        ];
    }
}
