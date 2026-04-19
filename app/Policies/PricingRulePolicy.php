<?php

namespace App\Policies;

use App\Models\PricingRule;
use App\Models\User;

class PricingRulePolicy
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
        return $user->can('view pricing');
    }

    public function view(User $user, PricingRule $pricingRule): bool
    {
        return $user->can('view pricing');
    }

    public function create(User $user): bool
    {
        return $user->can('manage pricing');
    }

    public function update(User $user, PricingRule $pricingRule): bool
    {
        return $user->can('manage pricing');
    }

    public function delete(User $user, PricingRule $pricingRule): bool
    {
        return $user->can('manage pricing');
    }
}
