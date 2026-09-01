<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $organizations = [
            [
                'name' => 'مطب تبریز',
                'code' => 'tabriz-office',
                'is_active' => true,
                'description' => null,
            ],
            [
                'name' => 'مطب تهران',
                'code' => 'tehran-office',
                'is_active' => true,
                'description' => null,
            ],
            [
                'name' => 'کلینیک پاکان',
                'code' => 'pakan-clinic',
                'is_active' => true,
                'description' => null,
            ],
            [
                'name' => 'منزل',
                'code' => 'home',
                'is_active' => true,
                'description' => null,
            ],
            [
                'name' => 'منزل پدر',
                'code' => 'parents-home',
                'is_active' => true,
                'description' => null,
            ],
        ];

        foreach ($organizations as $organization) {
            Organization::create($organization);
        }
    }
}
