<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BlogPostDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'featured_image' => $this->featured_image
                ? Storage::disk('public')->url($this->featured_image)
                : null,
            'category' => $this->whenLoaded('category', fn () => [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'author' => $this->whenLoaded('author', fn () => $this->author->name),
            'tags' => $this->tags,
            'published_at' => $this->published_at?->toIso8601String(),
            'view_count' => $this->view_count,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'canonical_url' => $this->canonical_url,
            'og_image' => $this->og_image
                ? Storage::disk('public')->url($this->og_image)
                : null,
            'seo_content' => $this->seo_content,
        ];
    }
}
