<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات کاربر')
                    ->schema([
                        TextInput::make('name')
                            ->label('نام')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('ایمیل')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('password')
                            ->label('رمز عبور')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation) => $operation === 'create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText('برای ویرایش، فقط در صورت نیاز به تغییر رمز عبور را وارد کنید.'),
                    ])
                    ->columns(2),

                Section::make('نقش و دسترسی')
                    ->description('نقش کاربر و مجموعه‌هایی که اجازه‌ی کار روی آن‌ها را دارد را مشخص کنید. مدیر به همه‌ی مجموعه‌ها دسترسی دارد و نیازی به انتخاب مجموعه ندارد.')
                    ->schema([
                        Select::make('roles')
                            ->label('نقش')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),

                        Select::make('organizations')
                            ->label('مجموعه‌های قابل‌دسترسی')
                            ->relationship('organizations', 'name')
                            ->multiple()
                            ->preload()
                            ->helperText('فقط برای نقش‌های «حسابدار» و «کارمند» استفاده می‌شود.'),
                    ])
                    ->columns(2),
            ]);
    }
}
