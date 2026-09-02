<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Support\JalaliPeriod;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Morilog\Jalali\Jalalian;

class ExpensesByCategoryChart extends ApexChartWidget
{
    protected static ?string $chartId = 'expensesByCategoryChart';

    protected static ?string $heading = 'سهم دسته‌ها از هزینه‌های ماه جاری';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return (bool) auth()->user()?->can('viewAny', Expense::class);
    }

    protected function getOptions(): array
    {
        $today = Jalalian::now();
        [$start, $end] = JalaliPeriod::monthRange($today->getYear(), $today->getMonth());

        $user = auth()->user();
        $accessibleOrgIds = $user && ! $user->isAdmin() ? $user->accessibleOrganizationIds() : null;

        $totalsByCategory = Expense::query()
            ->selectRaw('expense_category_id, SUM(total_amount) as total')
            ->when(
                $accessibleOrgIds !== null,
                fn ($query) => $query->whereIn('organization_id', $accessibleOrgIds)
            )
            ->whereBetween('expense_date', [$start, $end])
            ->groupBy('expense_category_id')
            ->pluck('total', 'expense_category_id');

        $categories = ExpenseCategory::query()
            ->whereIn('id', $totalsByCategory->keys())
            ->get(['id', 'name', 'color'])
            ->keyBy('id');

        $labels = [];
        $series = [];
        $colors = [];

        foreach ($totalsByCategory as $categoryId => $total) {
            $category = $categories->get($categoryId);
            $labels[] = $category->name ?? 'بدون دسته';
            $series[] = (int) $total;
            $colors[] = $category->color ?? '#94a3b8';
        }

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $series,
            'labels' => $labels,
            'colors' => $colors,
            'legend' => [
                'position' => 'bottom',
            ],
            'noData' => [
                'text' => 'هزینه‌ای برای این ماه ثبت نشده است',
            ],
        ];
    }
}
