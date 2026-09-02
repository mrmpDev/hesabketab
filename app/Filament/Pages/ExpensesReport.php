<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Models\Organization;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

class ExpensesReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'گزارش هزینه‌ها';

    protected static ?string $title = 'گزارش هزینه‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'حسابداری';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.expenses-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('organization_id')
                    ->label('مطب / مجموعه')
                    ->options(fn () => $this->getOrganizations()->pluck('name', 'id'))
                    ->placeholder('همه‌ی مجموعه‌های در دسترس')
                    ->columnSpanFull(),

                DatePicker::make('from')
                    ->label('از تاریخ')
                    ->jalali()
                    ->format('Y-m-d')
                    ->native(false),

                DatePicker::make('to')
                    ->label('تا تاریخ')
                    ->jalali()
                    ->format('Y-m-d')
                    ->native(false),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function generateReport()
    {
        $data = $this->form->getState();

        // ساخت آدرس روت PDF همراه با پارامترها
        $url = route('expenses.report.pdf', [
            'organization_id' => $data['organization_id'] ?? null,
            'from' => $data['from'] ?? null,
            'to' => $data['to'] ?? null,
        ]);

        // باز کردن لینک در تب جدید از طریق جاوااسکریپت در Livewire
        $this->js("window.open('{$url}', '_blank');");
    }
}
