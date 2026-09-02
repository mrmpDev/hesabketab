<?php

namespace App\Policies;

use App\Models\Buyer;
use App\Models\User;

class BuyerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAccountant();
    }

    public function view(User $user, Buyer $buyer): bool
    {
        return $user->isAdmin() || ($user->isAccountant() && $user->canAccessOrganization($buyer->organization_id));
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Buyer $buyer): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Buyer $buyer): bool
    {
        return $user->isAdmin();
    }
}
