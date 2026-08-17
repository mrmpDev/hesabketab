<?php

namespace App\Filament\Resources\Expenses\Tables;

use App\Models\Expense;
use App\Traits\HasJalaliDate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class ExpensesTable
{
    use HasJalaliDate;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('expense_date', 'desc')
            ->columns([
                TextColumn::make('organization.name')
                    ->label('مطب / مجموعه')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('expense_date')
                    ->label('تاریخ هزینه')
                    ->sortable()
                    ->formatStateUsing(
                        fn ($state) => self::toJalali($state, 'Y/m/d') ?? '-'
                    ),

                TextColumn::make('category.name')
                    ->label('دسته‌بندی')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('buyer.full_name')
                    ->label('خریدار')
                    ->searchable(),

                TextColumn::make('vendor.name')
                    ->label('فروشگاه / دریافت‌کننده')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('payment_method')
                    ->label('روش پرداخت')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'pos' => 'پوز',
                            'transfer' => 'کارت‌به‌کارت',
                            'cash' => 'نقدی',
                            default => 'نامشخص',
                        }
                    ),

                TextColumn::make('total_amount')
                    ->label('مبلغ کل')
                    ->numeric()
                    ->suffix(' ریال')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('ثبت‌کننده')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاریخ ثبت')
                    ->sortable()
                    ->formatStateUsing(
                        fn ($state) => self::toJalali($state, 'Y/m/d H:i') ?? '-'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('organization_id')
                    ->label('مطب / مجموعه')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('expense_category_id')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('payment_method')
                    ->label('روش پرداخت')
                    ->options([
                        'pos' => 'پوز',
                        'transfer' => 'کارت‌به‌کارت',
                        'cash' => 'نقدی',
                    ]),

                Filter::make('expense_date')
                    ->label('بازه تاریخ')
                    ->schema([
                        DatePicker::make('from')
                            ->label('از تاریخ')
                            ->native(false),

                        DatePicker::make('until')
                            ->label('تا تاریخ')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('expense_date', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date) => $query->whereDate('expense_date', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('ویرایش'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف'),
                ]),
            ])
            ->emptyStateHeading('هنوز هزینه‌ای ثبت نشده است')
            ->emptyStateDescription('برای شروع، اولین هزینه را ثبت کنید.');
    }
}
