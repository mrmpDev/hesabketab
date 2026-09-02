<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('نقش')
                    ->badge(),

                TextColumn::make('organizations.name')
                    ->label('مجموعه‌های قابل‌دسترسی')
                    ->badge()
                    ->placeholder('همه (مدیر)')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('نقش')
                    ->relationship('roles', 'name'),
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
            ->emptyStateHeading('کاربری یافت نشد');
    }
}
