<?php

namespace App\Support;

use App\Models\BankCard;
use App\Models\Buyer;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserPreference;

/**
 * Resolves the sensible default organization/buyer/bank card to pre-select
 * when a user opens the "create expense" form.
 *
 * - Organization: the organization the user used most recently (from
 *   UserPreference), falling back to the first active organization so the
 *   field is never left empty.
 * - Buyer / BankCard: each organization can flag exactly one buyer and one
 *   bank card as its default (Buyer::is_default / BankCard::is_default).
 */
class ExpenseDefaults
{
    public static function organizationId(?int $userId = null): ?int
    {
        $userId ??= auth()->id();
        $user = $userId ? User::find($userId) : null;
        $isAdmin = $user?->isAdmin() ?? true;
        $accessibleIds = $user && ! $isAdmin ? $user->accessibleOrganizationIds() : null;

        if ($userId) {
            $preferredOrganizationId = UserPreference::query()
                ->where('user_id', $userId)
                ->orderByDesc('id')
                ->value('organization_id');

            if (
                $preferredOrganizationId
                && (! $accessibleIds || in_array($preferredOrganizationId, $accessibleIds, true))
                && Organization::query()
                    ->whereKey($preferredOrganizationId)
                    ->where('is_active', true)
                    ->exists()
            ) {
                return $preferredOrganizationId;
            }
        }

        return Organization::query()
            ->where('is_active', true)
            ->when($accessibleIds !== null, fn ($query) => $query->whereIn('id', $accessibleIds))
            ->orderBy('name')
            ->value('id');
    }

    public static function buyerId(?int $organizationId): ?int
    {
        if (! $organizationId) {
            return null;
        }

        return Buyer::query()
            ->where('organization_id', $organizationId)
            ->where('is_active', true)
            ->where('is_default', true)
            ->value('id');
    }

    public static function bankCardId(?int $organizationId): ?int
    {
        if (! $organizationId) {
            return null;
        }

        return BankCard::query()
            ->where('organization_id', $organizationId)
            ->where('is_active', true)
            ->where('is_default', true)
            ->value('id');
    }
}
