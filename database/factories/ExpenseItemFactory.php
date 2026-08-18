<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpenseItem>
 */
class ExpenseItemFactory extends Factory
{
    protected $model = ExpenseItem::class;

    public function definition(): array
    {
        return [
            'expense_id' => Expense::factory(),
            'title' => fake()->words(2, true),
            'quantity' => fake()->randomFloat(2, 1, 10),
            'unit' => fake()->randomElement(['عدد', 'بسته', 'کیلو', 'خدمت']),
            'amount' => fake()->numberBetween(10_000, 5_000_000),
        ];
    }
}
