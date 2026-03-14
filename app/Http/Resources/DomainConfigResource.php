<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DomainConfigResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'domain' => [
                'name' => $this->name,
                'slug' => $this->slug,
                'default_currency' => $this->default_currency,
                'default_language' => $this->default_language,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'logo' => $this->logo_path ? asset('storage/'.$this->logo_path) : null,
                'favicon' => $this->favicon_path ? asset('storage/'.$this->favicon_path) : null,
            ],
            'seo' => [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
                'canonical_url' => $this->canonical_url,
                'og_image' => $this->og_image_path ? asset('storage/'.$this->og_image_path) : null,
            ],
            'tracking' => [
                'google_analytics_id' => $this->google_analytics_id,
                'google_tag_manager_id' => $this->google_tag_manager_id,
                'meta_pixel_id' => $this->meta_pixel_id,
            ],
            'pages' => [
                'about_us' => ! empty($this->about_us) ? [
                    'content' => $this->about_us,
                    'meta_title' => $this->about_us_meta_title,
                    'meta_description' => $this->about_us_meta_description,
                    'canonical_url' => $this->about_us_canonical_url,
                ] : null,
                'privacy_policy' => ! empty($this->privacy_policy) ? [
                    'content' => $this->privacy_policy,
                    'meta_title' => $this->privacy_policy_meta_title,
                    'meta_description' => $this->privacy_policy_meta_description,
                    'canonical_url' => $this->privacy_policy_canonical_url,
                ] : null,
                'terms_conditions' => ! empty($this->terms_conditions) ? [
                    'content' => $this->terms_conditions,
                    'meta_title' => $this->terms_conditions_meta_title,
                    'meta_description' => $this->terms_conditions_meta_description,
                    'canonical_url' => $this->terms_conditions_canonical_url,
                ] : null,
            ],
            'hero_slides' => DomainHeroSlideResource::collection(
                $this->heroSlides->where('is_active', true)
            ),
            'locations' => LocationResource::collection($this->locations),
        ];
    }
}
