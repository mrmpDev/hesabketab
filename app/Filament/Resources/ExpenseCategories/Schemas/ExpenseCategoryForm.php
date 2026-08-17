<?php

namespace App\Filament\Resources\ExpenseCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExpenseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام دسته‌بندی')
                    ->required()
                    ->maxLength(150),

                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),

                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
