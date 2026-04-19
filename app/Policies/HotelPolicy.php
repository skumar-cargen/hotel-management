<?php

namespace App\Policies;

use App\Models\Hotel;
use App\Models\User;

class HotelPolicy
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
        return $user->can('view hotels');
    }

    public function view(User $user, Hotel $hotel): bool
    {
        return $user->can('view hotels') && $user->canAccessHotel($hotel->id);
    }

    public function create(User $user): bool
    {
        return $user->can('manage hotels');
    }

    public function update(User $user, Hotel $hotel): bool
    {
        return $user->can('manage hotels') && $user->canAccessHotel($hotel->id);
    }

    public function delete(User $user, Hotel $hotel): bool
    {
        return $user->can('manage hotels') && $user->canAccessHotel($hotel->id);
    }
}
