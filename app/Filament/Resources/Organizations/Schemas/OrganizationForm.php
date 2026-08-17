<?php namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('نام مجموعه')
                ->required()
                ->maxLength(255)
                ->placeholder('مثلاً: مطب شماره ۱'),

            TextInput::make('code')
                ->label('کد')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->placeholder('clinic-1')
                ->helperText('کد یکتا برای استفاده داخلی سیستم'),


            Textarea::make('description')
                ->label('توضیحات')
                ->rows(4)
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label('فعال')
                ->default(true),])
            ->columns(2);
    }
}
