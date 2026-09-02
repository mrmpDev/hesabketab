<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;

class VendorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAccountant();
    }

    public function view(User $user, Vendor $vendor): bool
    {
        return $user->isAdmin() || ($user->isAccountant() && $user->canAccessOrganization($vendor->organization_id));
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Vendor $vendor): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        return $user->isAdmin();
    }
}
