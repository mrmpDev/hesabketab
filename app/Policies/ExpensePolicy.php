<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isAccountant() || $user->isStaff();
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->isAdmin() || $user->canAccessOrganization($expense->organization_id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function update(User $user, Expense $expense): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStaff() && $user->canAccessOrganization($expense->organization_id);
    }

    /**
     * Deletion is intentionally admin-only — accountants only read, and
     * staff should correct mistakes by editing rather than removing
     * financial records.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $user->isAdmin();
    }
}
