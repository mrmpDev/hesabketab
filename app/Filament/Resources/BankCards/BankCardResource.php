<?php

namespace App\Filament\Resources\BankCards;

use App\Filament\Resources\BankCards\Pages\CreateBankCard;
use App\Filament\Resources\BankCards\Pages\EditBankCard;
use App\Filament\Resources\BankCards\Pages\ListBankCards;
use App\Filament\Resources\BankCards\Schemas\BankCardForm;
use App\Filament\Resources\BankCards\Tables\BankCardsTable;
use App\Models\BankCard;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BankCardResource extends Resource
{
    protected static ?string $model = BankCard::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'کارت‌ها';

    protected static ?string $modelLabel = 'کارت';

    protected static ?string $pluralModelLabel = 'کارت‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return BankCardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BankCardsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankCards::route('/'),
            'create' => CreateBankCard::route('/create'),
            'edit' => EditBankCard::route('/{record}/edit'),
        ];
    }
}
