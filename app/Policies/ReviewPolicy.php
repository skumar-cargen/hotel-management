<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
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
        return $user->can('view reviews');
    }

    public function view(User $user, Review $review): bool
    {
        return $user->can('view reviews');
    }

    public function update(User $user, Review $review): bool
    {
        return $user->can('manage reviews');
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->can('manage reviews');
    }
}
