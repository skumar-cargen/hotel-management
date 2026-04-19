<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;

class DomainPolicy
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
        return $user->can('view domains');
    }

    public function view(User $user, Domain $domain): bool
    {
        return $user->can('view domains') && $user->canAccessDomain($domain->id);
    }

    public function create(User $user): bool
    {
        return $user->can('manage domains');
    }

    public function update(User $user, Domain $domain): bool
    {
        return $user->can('manage domains') && $user->canAccessDomain($domain->id);
    }

    public function delete(User $user, Domain $domain): bool
    {
        return $user->can('manage domains') && $user->canAccessDomain($domain->id);
    }
}
