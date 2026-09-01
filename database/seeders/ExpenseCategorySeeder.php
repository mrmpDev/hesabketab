<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'آرایشی و بهداشتی',
                'color' => '#4F46E5',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'دارو',
                'color' => '#0891B2',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'آجیل و خشکبار',
                'color' => '#16A34A',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'میوه و سیفیجات ',
                'color' => '#EA580C',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'لبنیات',
                'color' => '#9333EA',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'آجیل و خشکبار',
                'color' => '#CA8A04',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'هزینه تعمیرات',
                'color' => '#DC2626',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'هزینه پیک',
                'color' => '#0D9488',
                'description' => null,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create($category);
        }
    }
}
