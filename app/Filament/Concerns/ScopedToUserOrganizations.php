<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Restricts a Filament resource's table/records to the organizations the
 * current user is allowed to access. Admins are left unrestricted.
 *
 * Use on resources whose model has an `organization_id` column.
 */
trait ScopedToUserOrganizations
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if ($user && ! $user->isAdmin()) {
            $query->whereIn('organization_id', $user->accessibleOrganizationIds());
        }

        return $query;
    }
}
