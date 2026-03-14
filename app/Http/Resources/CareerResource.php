<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'location' => $this->location,
            'job_type' => $this->job_type,
            'department' => $this->department,
            'about_role' => $this->about_role,
            'responsibilities' => $this->responsibilities,
            'requirements' => $this->requirements,
            'what_we_offer' => $this->what_we_offer,
            'last_apply_date' => $this->last_apply_date->toDateString(),
        ];
    }
}
