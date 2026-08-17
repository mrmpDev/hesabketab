<?php

namespace App\Filament\Resources\ExpenseCategories\Tables;

use App\Traits\HasJalaliDate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ExpenseCategoriesTable
{
    use HasJalaliDate;

    protected static function jalaliDate($date, string $format = 'Y/m/d'): ?string
    {
        return (new self)->toJalali($date, $format);
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('دسته‌بندی')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->sortable()
                    ->formatStateUsing(
                        fn ($state) => self::jalaliDate($state, 'Y/m/d H:i') ?? '-'
                    ),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('وضعیت'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()
                    ->label('ویرایش'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('حذف'),
                ]),
            ])
            ->emptyStateHeading('هنوز دسته‌بندی‌ای ثبت نشده است')
            ->emptyStateDescription('برای شروع، اولین دسته‌بندی هزینه را ثبت کنید.');
    }
}
