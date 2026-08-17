<?php

namespace App\Filament\Resources\Buyers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BuyerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('organization_id')
                    ->label('مطب')
                    ->relationship('organization', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('first_name')
                    ->label('نام')
                    ->required()
                    ->maxLength(100),

                TextInput::make('last_name')
                    ->label('نام خانوادگی')
                    ->required()
                    ->maxLength(100),

                Toggle::make('is_default')
                    ->label('خریدار پیش‌فرض')
                    ->helperText('اگر فعال باشد، این شخص به صورت پیش‌فرض برای ثبت هزینه‌های این مطب انتخاب خواهد شد.')
                    ->default(false),

                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ])
            ->columns(2);
    }
}
