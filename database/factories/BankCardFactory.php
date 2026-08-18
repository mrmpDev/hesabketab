<?php

namespace Database\Factories;

use App\Models\BankCard;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BankCard>
 */
class BankCardFactory extends Factory
{
    protected $model = BankCard::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'bank_name' => fake()->randomElement(['ملت', 'ملی', 'صادرات', 'پاسارگاد', 'سامان']),
            'card_number' => fake()->numerify('6037#############'),
            'holder_name' => fake()->name(),
            'is_default' => false,
            'is_active' => true,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
