<?php

namespace Database\Seeders;

use App\Models\BankCard;
use App\Models\Buyer;
use App\Models\ExpenseCategory;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        ExpenseCategory::factory()->count(5)->create();

        Organization::factory()
            ->count(3)
            ->create()
            ->each(function (Organization $organization) {
                Buyer::factory()->for($organization)->default()->create();
                Buyer::factory()->for($organization)->count(2)->create();

                BankCard::factory()->for($organization)->default()->create();
                BankCard::factory()->for($organization)->count(1)->create();

                Vendor::factory()->for($organization)->count(3)->create();
            });
    }
}
