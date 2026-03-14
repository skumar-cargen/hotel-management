<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'cover_letter' => $this->cover_letter,
            'resume_url' => $this->resume_path ? asset('storage/'.$this->resume_path) : null,
            'status' => $this->status,
            'career' => $this->whenLoaded('career', fn () => [
                'id' => $this->career->id,
                'title' => $this->career->title,
                'slug' => $this->career->slug,
                'department' => $this->career->department,
                'job_type' => $this->career->job_type,
                'location' => $this->career->location,
            ]),
            'domain' => $this->whenLoaded('domain', fn () => [
                'id' => $this->domain->id,
                'name' => $this->domain->name,
            ]),
            'applied_at' => $this->created_at->toIso8601String(),
        ];
    }
}
