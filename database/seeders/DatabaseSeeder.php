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

        $this->call([
            UserSeeder::class,
            ExpenseCategorySeeder::class,
            OrganizationSeeder::class,
            BuyerSeeder::class,
            VendorSeeder::class,
            BankCardSeeder::class,
        ]);
    }
}
