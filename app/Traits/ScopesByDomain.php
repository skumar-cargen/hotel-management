<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait to scope queries by the logged-in user's managed domains.
 * Admin sees everything, Domain Manager sees only their domains' data.
 */
trait ScopesByDomain
{
    /**
     * Scope a hotel query to user's managed domains.
     */
    protected function scopeHotelsForUser(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $query;
        }

        $hotelIds = $user->managedHotelIds();

        return $query->whereIn('hotels.id', $hotelIds);
    }

    /**
     * Scope a booking query to user's managed domains.
     */
    protected function scopeBookingsForUser(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $query;
        }

        $domainIds = $user->managedDomainIds();

        return $query->whereIn('bookings.domain_id', $domainIds);
    }

    /**
     * Scope reviews to user's managed hotels.
     */
    protected function scopeReviewsForUser(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $query;
        }

        $hotelIds = $user->managedHotelIds();

        return $query->whereIn('reviews.hotel_id', $hotelIds);
    }

    /**
     * Get domains the current user can manage.
     */
    protected function userDomains()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return \App\Models\Domain::active()->get();
        }

        return $user->domains()->where('domain_user.is_active', true)->get();
    }

    /**
     * Check if current user can access this hotel (abort 403 if not).
     */
    protected function authorizeHotel($hotel): void
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->canAccessHotel($hotel->id)) {
            abort(403, 'You do not have access to this hotel.');
        }
    }

    /**
     * Check if current user can access this booking (abort 403 if not).
     */
    protected function authorizeBooking($booking): void
    {
        $user = Auth::user();
        if (! $user->isAdmin() && $booking->domain_id && ! $user->canAccessDomain($booking->domain_id)) {
            abort(403, 'You do not have access to this booking.');
        }
    }
}
