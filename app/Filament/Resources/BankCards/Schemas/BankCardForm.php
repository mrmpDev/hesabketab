<?php

namespace App\Filament\Resources\BankCards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankCardForm
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

                TextInput::make('bank_name')
                    ->label('نام بانک')
                    ->required()
                    ->maxLength(100),

                TextInput::make('holder_name')
                    ->label('نام و نام خانوادگی دارنده کارت')
                    ->required()
                    ->maxLength(150),

                TextInput::make('card_number')
                    ->label('شماره کارت')
                    ->required()
                    ->length(16)
                    ->maxLength(16)
                    ->minLength(16)
                    ->placeholder('6037991234567890')
                    ->rule('digits:16'),

                Toggle::make('is_default')
                    ->label('کارت پیش‌فرض')
                    ->helperText('این کارت به صورت پیش‌فرض برای ثبت هزینه‌های این مطب انتخاب خواهد شد.')
                    ->default(false),

                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ])
            ->columns(2);
    }
}
