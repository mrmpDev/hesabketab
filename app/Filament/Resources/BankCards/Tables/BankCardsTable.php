<?php

namespace App\Filament\Resources\BankCards\Tables;

use App\Traits\HasJalaliDate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BankCardsTable
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

                TextColumn::make('bank_name')
                    ->label('بانک')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('holder_name')
                    ->label('دارنده کارت')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('card_number')
                    ->label('شماره کارت')
                    ->formatStateUsing(function ($state) {
                        if (! $state) {
                            return '-';
                        }

                        return implode(' ', str_split($state, 4));
                    })
                    ->copyable()
                    ->searchable()
                    ->extraAttributes([
                        'dir' => 'ltr',
                        'style' => 'direction: ltr; text-align: left;',
                    ]),

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
                    ->label('کارت پیش‌فرض'),

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
            ->emptyStateHeading('هنوز کارتی ثبت نشده است')
            ->emptyStateDescription('برای شروع، اولین کارت بانکی را ثبت کنید.');
    }
}
