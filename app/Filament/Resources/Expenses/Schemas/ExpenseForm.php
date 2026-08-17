<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Models\BankCard;
use App\Models\Buyer;
use App\Models\ExpenseCategory;
use App\Models\Organization;
use App\Models\UserPreference;
use App\Models\Vendor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات اصلی هزینه')
                    ->schema([
                        Select::make('organization_id')
                            ->label('مطب / مجموعه')
                            ->options(
                                Organization::query()
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->default(
                                fn () => UserPreference::query()
                                    ->where('user_id', auth()->id())
                                    ->orderByDesc('id')
                                    ->value('organization_id')
                            )
                            ->afterStateUpdated(function ($state, $set) {
                                if (! $state) {
                                    $set('buyer_id', null);
                                    $set('vendor_id', null);
                                    $set('bank_card_id', null);

                                    return;
                                }

                                $preference = UserPreference::query()
                                    ->where('user_id', auth()->id())
                                    ->where('organization_id', $state)
                                    ->first();

                                $set('buyer_id', $preference?->buyer_id);
                                $set('bank_card_id', $preference?->bank_card_id);
                                $set('payment_method', $preference?->default_payment_method);
                                $set('vendor_id', null);
                            }),

                        DatePicker::make('expense_date')
                            ->label('تاریخ هزینه')
                            ->jalali()
                            ->native(false)
                            ->displayFormat('Y/m/d')
                            ->required()
                            ->default(now()),

                        Select::make('expense_category_id')
                            ->label('دسته‌بندی هزینه')
                            ->options(
                                ExpenseCategory::query()
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('buyer_id')
                            ->label('خریدار')
                            ->options(function ($get) {
                                $organizationId = $get('organization_id');

                                if (! $organizationId) {
                                    return [];
                                }

                                return Buyer::query()
                                    ->where('organization_id', $organizationId)
                                    ->orderBy('first_name')
                                    ->orderBy('last_name')
                                    ->get()
                                    ->mapWithKeys(fn (Buyer $buyer) => [
                                        $buyer->id => $buyer->full_name,
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->preload()
                            ->live(),

                        Select::make('vendor_id')
                            ->label('فروشگاه / دریافت‌کننده')
                            ->options(function ($get) {
                                $organizationId = $get('organization_id');

                                if (! $organizationId) {
                                    return [];
                                }

                                return Vendor::query()
                                    ->where('organization_id', $organizationId)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload(),

                        Select::make('payment_method')
                            ->label('روش پرداخت')
                            ->options([
                                'pos' => 'پوز',
                                'transfer' => 'کارت‌به‌کارت',
                                'cash' => 'نقدی',
                            ])
                            ->required()
                            ->live(),

                        Select::make('bank_card_id')
                            ->label('کارت پرداخت‌کننده')
                            ->options(function ($get) {
                                $organizationId = $get('organization_id');

                                if (! $organizationId) {
                                    return [];
                                }

                                return BankCard::query()
                                    ->where('organization_id', $organizationId)
                                    ->orderBy('bank_name')
                                    ->orderBy('holder_name')
                                    ->get()
                                    ->mapWithKeys(fn (BankCard $card) => [
                                        $card->id => $card->display_name,
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => in_array($get('payment_method'), ['pos', 'transfer']))
                            ->required(fn ($get) => in_array($get('payment_method'), ['pos', 'transfer'])),

                        TextInput::make('total_amount')
                            ->label('مبلغ کل')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->suffix('ریال')
                            ->required(),

                        Textarea::make('notes')
                            ->label('توضیحات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('اقلام خرید')
                    ->description('هر تعداد کالا یا خدمت که در این خرید وجود دارد می‌توانید اضافه کنید.')
                    ->schema([
                        Repeater::make('items')
                            ->label('اقلام')
                            ->relationship()
                            ->schema([
                                TextInput::make('title')
                                    ->label('کالا / خدمت')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('quantity')
                                    ->label('تعداد')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(1),

                                TextInput::make('unit')
                                    ->label('واحد')
                                    ->placeholder('عدد، بسته، کیلو، خدمت و ...'),

                                TextInput::make('amount')
                                    ->label('مبلغ آیتم')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(0)
                                    ->suffix('ریال'),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel('افزودن آیتم')
                            ->reorderable()
                            ->collapsible()
                            ->itemNumbers()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
