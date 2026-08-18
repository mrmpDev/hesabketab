<?php

use App\Models\BankCard;
use App\Models\Buyer;
use App\Models\Organization;

it('only keeps the most recently saved buyer as the default for an organization', function () {
    $organization = Organization::factory()->create();

    $first = Buyer::factory()->for($organization)->create(['is_default' => true]);
    $second = Buyer::factory()->for($organization)->create(['is_default' => true]);

    expect($first->refresh()->is_default)->toBeFalse()
        ->and($second->refresh()->is_default)->toBeTrue();
});

it('does not affect default buyers belonging to other organizations', function () {
    $organizationA = Organization::factory()->create();
    $organizationB = Organization::factory()->create();

    $buyerA = Buyer::factory()->for($organizationA)->create(['is_default' => true]);
    $buyerB = Buyer::factory()->for($organizationB)->create(['is_default' => true]);

    expect($buyerA->refresh()->is_default)->toBeTrue()
        ->and($buyerB->refresh()->is_default)->toBeTrue();
});

it('only keeps the most recently saved bank card as the default for an organization', function () {
    $organization = Organization::factory()->create();

    $first = BankCard::factory()->for($organization)->create(['is_default' => true]);
    $second = BankCard::factory()->for($organization)->create(['is_default' => true]);

    expect($first->refresh()->is_default)->toBeFalse()
        ->and($second->refresh()->is_default)->toBeTrue();
});
