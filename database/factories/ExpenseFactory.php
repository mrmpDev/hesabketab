<?php

namespace Database\Factories;

use App\Models\Buyer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'expense_category_id' => ExpenseCategory::factory(),
            'buyer_id' => Buyer::factory(),
            'bank_card_id' => null,
            'vendor_id' => null,
            'payment_method' => fake()->randomElement(['pos', 'transfer', 'cash']),
            'expense_date' => fake()->dateTimeBetween('-2 months', 'now'),
            'total_amount' => fake()->numberBetween(100_000, 20_000_000),
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
