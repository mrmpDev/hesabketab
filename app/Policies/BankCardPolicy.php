<?php

namespace App\Policies;

use App\Models\BankCard;
use App\Models\User;

class BankCardPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAccountant();
    }

    public function view(User $user, BankCard $bankCard): bool
    {
        return $user->isAdmin() || ($user->isAccountant() && $user->canAccessOrganization($bankCard->organization_id));
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, BankCard $bankCard): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, BankCard $bankCard): bool
    {
        return $user->isAdmin();
    }
}
