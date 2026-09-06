<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Models\BankCard;
use App\Models\Buyer;
use App\Models\ExpenseCategory;
use App\Models\Organization;
use App\Models\Vendor;
use App\Support\ExpenseDefaults;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class ExpenseForm
{
    /**
     * Numeric/currency-style inputs render with a Persian (RTL) direction
     * by default, which pushes the cursor behind the "ریال" suffix and
     * makes the field look impossible to type in. Forcing the actual
     * <input> to be LTR fixes this while the label/suffix stay RTL.
     */
    private const LTR_NUMERIC_INPUT_ATTRIBUTES = [
        'dir' => 'ltr',
        'style' => 'text-align: left;',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات اصلی هزینه')
                    ->schema([
                        Select::make('organization_id')
                            ->label('مطب / مجموعه')
                            ->options(function () {
                                $user = auth()->user();

                                return Organization::query()
                                    ->where('is_active', true)
                                    ->when(
                                        $user && ! $user->isAdmin(),
                                        fn ($query) => $query->whereIn('id', $user->accessibleOrganizationIds())
                                    )
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->default(fn () => ExpenseDefaults::organizationId())
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if (! $state) {
                                    $set('buyer_id', null);
                                    $set('vendor_id', null);
                                    $set('bank_card_id', null);

                                    return;
                                }

                                // هر سازمان خریدار پیش‌فرض خودش را دارد؛ با تغییر
                                // سازمان، خریدار پیش‌فرض همان سازمان انتخاب می‌شود.
                                $set('buyer_id', ExpenseDefaults::buyerId($state));
                                $set('vendor_id', null);

                                // اگر روش پرداخت هم‌اکنون پوز یا کارت‌به‌کارت است،
                                // کارت پیش‌فرض سازمان جدید انتخاب می‌شود.
                                $set(
                                    'bank_card_id',
                                    in_array($get('payment_method'), ['pos', 'transfer'], true)
                                        ? ExpenseDefaults::bankCardId($state)
                                        : null
                                );
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
                            ->live()
                            ->default(fn ($get) => ExpenseDefaults::buyerId($get('organization_id'))),

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
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                // با انتخاب پوز یا کارت‌به‌کارت، در صورتی که کارتی
                                // انتخاب نشده باشد، کارت پیش‌فرض سازمان به صورت
                                // خودکار نمایش داده و انتخاب می‌شود.
                                if (in_array($state, ['pos', 'transfer'], true) && blank($get('bank_card_id'))) {
                                    $set('bank_card_id', ExpenseDefaults::bankCardId($get('organization_id')));
                                }
                            }),

                        Select::make('bank_card_id')
                            ->label('کارت پرداخت‌کننده')
                            ->columnSpanFull()
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
                            ->default(function ($get) {
                                if (! in_array($get('payment_method'), ['pos', 'transfer'], true)) {
                                    return null;
                                }

                                return ExpenseDefaults::bankCardId($get('organization_id'));
                            })
                            ->visible(fn ($get) => in_array($get('payment_method'), ['pos', 'transfer']))
                            ->required(fn ($get) => in_array($get('payment_method'), ['pos', 'transfer'])),

                        //                        TextInput::make('total_amount')
                        //                            ->columnSpanFull()
                        //                            ->label('مبلغ کل')
                        //                            ->numeric()
                        //                            ->integer()
                        //                            ->minValue(0)
                        //                            ->suffix('ریال')
                        //                            ->required()
                        //                            ->extraInputAttributes(self::LTR_NUMERIC_INPUT_ATTRIBUTES),

                        TextInput::make('total_amount')
                            ->columnSpanFull()
                            ->label('مبلغ کل')
                            ->inputMode('numeric')
                            ->minValue(0)
                            ->suffix('ریال')
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => (int) str_replace(',', '', $state))
                            ->extraInputAttributes([
                                'dir' => 'ltr',
                                'style' => 'direction: ltr; text-align: left;',
                                'x-data' => '{}',
                                'x-on:input' => <<<'JS'
            let value = $el.value.replace(/,/g, '').replace(/\D/g, '');

            if (value === '') {
                $el.value = '';
                return;
            }

            $el.value = Number(value).toLocaleString('en-US');
        JS,
                            ]),

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
                                    ->maxLength(255),

                                TextInput::make('quantity')
                                    ->label('تعداد')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(1)
                                    ->extraInputAttributes(self::LTR_NUMERIC_INPUT_ATTRIBUTES),

                                TextInput::make('unit')
                                    ->label('واحد')
                                    ->placeholder('عدد، بسته، کیلو، خدمت و ...'),

                                //                                TextInput::make('amount')
                                //                                    ->label('مبلغ آیتم')
                                //                                    ->numeric()
                                //                                    ->integer()
                                //                                    ->minValue(0)
                                //                                    ->suffix('ریال')
                                //                                    ->extraInputAttributes(self::LTR_NUMERIC_INPUT_ATTRIBUTES),
                                //                            ])

                                TextInput::make('amount')
                                    ->label('مبلغ آیتم')
                                    ->inputMode('numeric')
                                    ->minValue(0)
                                    ->suffix('ریال')
                                    ->dehydrateStateUsing(fn ($state) => (int) str_replace(',', '', $state))
                                    ->extraInputAttributes([
                                        'dir' => 'ltr',
                                        'style' => 'direction: ltr; text-align: left;',
                                        'x-data' => '{}',
                                        'x-on:input' => <<<'JS'
            let value = $el.value.replace(/,/g, '').replace(/\D/g, '');

            if (value === '') {
                $el.value = '';
                return;
            }

            $el.value = Number(value).toLocaleString('en-US');
        JS,
                                    ]),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->addActionLabel('افزودن آیتم')
                            ->reorderable()
                            ->collapsible()
                            ->itemNumbers()
                            ->columnSpanFull(),
                    ]),

                Section::make('ضمیمه‌ها')
                    ->description('اختیاری — فیش، فاکتور یا رسید خرید را می‌توانید ضمیمه کنید.')
                    ->schema([
                        Repeater::make('attachments')
                            ->label('فایل‌های ضمیمه')
                            ->relationship()
                            ->schema([
                                FileUpload::make('file_path')
                                    ->label('فایل')
                                    ->disk('public')
                                    ->directory('expense-attachments')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                if (! empty($data['file_path'])) {
                                    $data['file_name'] = basename($data['file_path']);
                                    $data['mime_type'] = Storage::disk('public')->mimeType($data['file_path']);
                                }

                                return $data;
                            })
                            ->addActionLabel('افزودن ضمیمه')
                            ->defaultItems(0)
                            ->collapsible()
                            ->columnSpanFull(),
                    ])
                    ->collapsed(fn ($record) => blank($record?->attachments)),
            ]);
    }
}
