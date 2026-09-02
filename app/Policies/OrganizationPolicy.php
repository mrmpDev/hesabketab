<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAccountant();
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->isAdmin() || ($user->isAccountant() && $user->canAccessOrganization($organization->id));
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $user->isAdmin();
    }
}
