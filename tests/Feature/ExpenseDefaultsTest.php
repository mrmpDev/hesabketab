<?php

use App\Models\BankCard;
use App\Models\Buyer;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserPreference;
use App\Support\ExpenseDefaults;

it('falls back to the first active organization when the user has no preference', function () {
    Organization::factory()->create(['name' => 'ب - مطب دوم', 'is_active' => true]);
    $first = Organization::factory()->create(['name' => 'الف - مطب اول', 'is_active' => true]);
    Organization::factory()->create(['name' => 'آ - مطب غیرفعال', 'is_active' => false]);

    $user = User::factory()->create();

    expect(ExpenseDefaults::organizationId($user->id))->toBe($first->id);
});

it('prefers the organization the user last used', function () {
    $user = User::factory()->create();
    Organization::factory()->create(['is_active' => true]);
    $lastUsed = Organization::factory()->create(['is_active' => true]);

    UserPreference::query()->create([
        'user_id' => $user->id,
        'organization_id' => $lastUsed->id,
    ]);

    expect(ExpenseDefaults::organizationId($user->id))->toBe($lastUsed->id);
});

it('resolves the default buyer for an organization', function () {
    $organization = Organization::factory()->create();
    Buyer::factory()->for($organization)->create(['is_default' => false]);
    $default = Buyer::factory()->for($organization)->create(['is_default' => true]);

    expect(ExpenseDefaults::buyerId($organization->id))->toBe($default->id);
});

it('resolves the default bank card for an organization', function () {
    $organization = Organization::factory()->create();
    BankCard::factory()->for($organization)->create(['is_default' => false]);
    $default = BankCard::factory()->for($organization)->create(['is_default' => true]);

    expect(ExpenseDefaults::bankCardId($organization->id))->toBe($default->id);
});

it('returns null when the organization has no default buyer or bank card', function () {
    $organization = Organization::factory()->create();
    Buyer::factory()->for($organization)->create(['is_default' => false]);
    BankCard::factory()->for($organization)->create(['is_default' => false]);

    expect(ExpenseDefaults::buyerId($organization->id))->toBeNull()
        ->and(ExpenseDefaults::bankCardId($organization->id))->toBeNull();
});
