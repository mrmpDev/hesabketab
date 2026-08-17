<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VendorForm
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

                TextInput::make('name')
                    ->label('نام فروشگاه / دریافت‌کننده')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('شماره تماس')
                    ->tel()
                    ->maxLength(20),

                TextInput::make('contact_name')
                    ->label('نام شخص دریافت‌کننده')
                    ->maxLength(150),

                TextInput::make('address')
                    ->label('آدرس')
                    ->maxLength(500),

                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ])
            ->columns(2);
    }
}
