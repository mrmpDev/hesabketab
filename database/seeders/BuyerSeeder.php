<?php

namespace Database\Seeders;

use App\Models\Buyer;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class BuyerSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $buyers = [
            [
                'organization_code' => 'tabriz-office',
                'first_name' => 'مریم',
                'last_name' => 'حسین زاده',
            ],
            [
                'organization_code' => 'tehran-office',
                'first_name' => 'مهسا',
                'last_name' => 'حق طلب',
            ],
            [
                'organization_code' => 'pakan-clinic',
                'first_name' => 'آقا',
                'last_name' => 'رضا',
            ],
            [
                'organization_code' => 'home',
                'first_name' => 'محمدرضا',
                'last_name' => 'محمد پوران',
            ],
            [
                'organization_code' => 'parents-home',
                'first_name' => 'محمدرضا',
                'last_name' => 'محمدپوران',
            ],
        ];

        foreach ($buyers as $buyer) {
            $organization = Organization::where(
                'code',
                $buyer['organization_code']
            )->firstOrFail();

            Buyer::create([
                'organization_id' => $organization->id,
                'first_name' => $buyer['first_name'],
                'last_name' => $buyer['last_name'],
                'is_default' => true,
                'is_active' => true,
            ]);
        }
    }
}
