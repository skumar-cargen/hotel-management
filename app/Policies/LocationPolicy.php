<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;

class LocationPolicy
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
        return $user->can('view locations');
    }

    public function view(User $user, Location $location): bool
    {
        return $user->can('view locations');
    }

    public function create(User $user): bool
    {
        return $user->can('manage locations');
    }

    public function update(User $user, Location $location): bool
    {
        return $user->can('manage locations');
    }

    public function delete(User $user, Location $location): bool
    {
        return $user->can('manage locations');
    }
}
