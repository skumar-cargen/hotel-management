<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;

class TestimonialController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $domain = $this->domain();

        $testimonials = $domain->testimonials()
            ->with('hotel:id,name,slug,star_rating')
            ->get()
            ->map(fn ($review) => [
                'id' => $review->id,
                'guest_name' => $review->guest_name,
                'rating' => $review->rating,
                'title' => $review->title,
                'comment' => $review->comment,
                'is_verified' => $review->is_verified,
                'hotel' => $review->hotel ? [
                    'name' => $review->hotel->name,
                    'slug' => $review->hotel->slug,
                    'star_rating' => $review->hotel->star_rating,
                ] : null,
                'created_at' => $review->created_at?->toIso8601String(),
            ]);

        return $this->successResponse($testimonials);
    }
}
