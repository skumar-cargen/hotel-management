<?php

namespace App\Observers;

use App\Models\Hotel;

class HotelObserver
{
    public function updating(Hotel $hotel): void
    {
        $this->recalculateCachedFields($hotel);
    }

    public static function recalculateForHotel(Hotel $hotel): void
    {
        $avgRating = $hotel->reviews()->where('is_approved', true)->avg('rating') ?? 0;
        $totalReviews = $hotel->reviews()->where('is_approved', true)->count();
        $minPrice = $hotel->roomTypes()->where('is_active', true)->min('base_price') ?? 0;

        $hotel->updateQuietly([
            'avg_rating' => round($avgRating, 2),
            'total_reviews' => $totalReviews,
            'min_price' => $minPrice,
        ]);
    }

    protected function recalculateCachedFields(Hotel $hotel): void
    {
        // Skip recalculation if these fields are being set explicitly
        if ($hotel->isDirty(['avg_rating', 'total_reviews', 'min_price'])) {
            return;
        }

        $avgRating = $hotel->reviews()->where('is_approved', true)->avg('rating') ?? 0;
        $totalReviews = $hotel->reviews()->where('is_approved', true)->count();
        $minPrice = $hotel->roomTypes()->where('is_active', true)->min('base_price') ?? 0;

        $hotel->avg_rating = round($avgRating, 2);
        $hotel->total_reviews = $totalReviews;
        $hotel->min_price = $minPrice;
    }
}
