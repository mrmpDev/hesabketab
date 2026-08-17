<?php

namespace App\Filament\Resources\Buyers\Tables;

use App\Traits\HasJalaliDate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BuyersTable
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
                TextColumn::make('organization.name')
                    ->label('مطب')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('full_name')
                    ->label('نام خریدار')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                IconColumn::make('is_default')
                    ->label('پیش‌فرض')
                    ->boolean()
                    ->sortable(),

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
                SelectFilter::make('organization_id')
                    ->label('مطب')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_default')
                    ->label('پیش‌فرض'),

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
            ->emptyStateHeading('هنوز خریداری ثبت نشده است')
            ->emptyStateDescription('برای شروع، اولین خریدار را ثبت کنید.');
    }
}
