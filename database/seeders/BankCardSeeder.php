<?php

namespace Database\Seeders;

use App\Models\BankCard;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class BankCardSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $cards = [
            [
                'bank_name' => 'شهر',
                'card_number' => '5047061139066878',
                'holder_name' => 'مریم آقاخانی',
                'is_default' => false,
            ],
            [
                'bank_name' => 'شهر',
                'card_number' => '5047061060721418',
                'holder_name' => 'مریم آقاخانی',
                'is_default' => true,
            ],
        ];

        Organization::query()->each(function (Organization $organization) use ($cards): void {
            foreach ($cards as $card) {
                BankCard::updateOrCreate(
                    [
                        'organization_id' => $organization->id,
                        'card_number' => $card['card_number'],
                    ],
                    [
                        'bank_name' => $card['bank_name'],
                        'holder_name' => $card['holder_name'],
                        'is_default' => $card['is_default'],
                        'is_active' => true,
                    ]
                );
            }
        });
    }
}
