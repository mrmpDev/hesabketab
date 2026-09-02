<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Models\Organization;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;

class ExpensesReport extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'گزارش هزینه‌ها';

    protected static ?string $title = 'گزارش هزینه‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'حسابداری';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.expenses-report';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('viewAny', Expense::class);
    }

    /**
     * @return Collection<int, Organization>
     */
    public function getOrganizations(): Collection
    {
        $user = auth()->user();

        return Organization::query()
            ->where('is_active', true)
            ->when(
                $user && ! $user->isAdmin(),
                fn ($query) => $query->whereIn('id', $user->accessibleOrganizationIds())
            )
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
