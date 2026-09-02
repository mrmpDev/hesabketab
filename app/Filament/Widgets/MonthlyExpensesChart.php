<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Support\JalaliPeriod;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MonthlyExpensesChart extends ApexChartWidget
{
    protected static ?string $chartId = 'monthlyExpensesChart';

    protected static ?string $heading = 'روند هزینه‌ها (۶ ماه اخیر)';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return (bool) auth()->user()?->can('viewAny', Expense::class);
    }

    protected function getOptions(): array
    {
        $user = auth()->user();
        $accessibleOrgIds = $user && ! $user->isAdmin() ? $user->accessibleOrganizationIds() : null;

        $labels = [];
        $totals = [];

        foreach (JalaliPeriod::lastMonths(6) as [$year, $month]) {
            [$start, $end] = JalaliPeriod::monthRange($year, $month);

            $totals[] = (int) Expense::query()
                ->when(
                    $accessibleOrgIds !== null,
                    fn ($query) => $query->whereIn('organization_id', $accessibleOrgIds)
                )
                ->whereBetween('expense_date', [$start, $end])
                ->sum('total_amount');

            $labels[] = JalaliPeriod::monthLabel($month);
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'مجموع هزینه (ریال)',
                    'data' => $totals,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
            ],
            'colors' => ['#ec4899'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 6,
                    'columnWidth' => '50%',
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
        ];
    }
}
