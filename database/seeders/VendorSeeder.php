<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $vendors = [
            'داروخانه دل آذر',
            'لبنیات سقاخانه',
            'ساندویچ شبنم',
            'سوپر مارکت',
            'گل ویکتوریا',
            'پمپ بنزین',
            'آرایشی سام',
            'رایحه',
            'نان حامد',
            'میوه فروشی جنب پاک',
            'میوه فروشی شهرک پرواز',
            'میوه فروشی فرشته',
        ];

        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            foreach ($vendors as $vendorName) {
                Vendor::create([
                    'organization_id' => $organization->id,
                    'name' => $vendorName,
                    'phone' => null,
                    'contact_name' => null,
                    'address' => null,
                    'notes' => null,
                    'description' => null,
                    'is_active' => true,
                ]);
            }
        }
    }
}
