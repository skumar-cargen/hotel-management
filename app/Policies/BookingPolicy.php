<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Admin users bypass all checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view bookings');
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->can('view bookings')
            && (! $booking->domain_id || $user->canAccessDomain($booking->domain_id));
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->can('manage bookings')
            && (! $booking->domain_id || $user->canAccessDomain($booking->domain_id));
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->can('cancel bookings')
            && (! $booking->domain_id || $user->canAccessDomain($booking->domain_id));
    }

    public function refund(User $user, Booking $booking): bool
    {
        return $user->can('refund bookings')
            && (! $booking->domain_id || $user->canAccessDomain($booking->domain_id));
    }
}
