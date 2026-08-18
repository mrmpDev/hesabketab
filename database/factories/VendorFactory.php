<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'contact_name' => fake()->optional()->name(),
            'address' => fake()->optional()->address(),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
