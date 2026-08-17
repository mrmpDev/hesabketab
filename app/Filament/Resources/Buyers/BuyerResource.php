<?php

namespace App\Filament\Resources\Buyers;

use App\Filament\Resources\Buyers\Pages\CreateBuyer;
use App\Filament\Resources\Buyers\Pages\EditBuyer;
use App\Filament\Resources\Buyers\Pages\ListBuyers;
use App\Filament\Resources\Buyers\Schemas\BuyerForm;
use App\Filament\Resources\Buyers\Tables\BuyersTable;
use App\Models\Buyer;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BuyerResource extends Resource
{
    protected static ?string $model = Buyer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'خریداران';

    protected static ?string $modelLabel = 'خریدار';

    protected static ?string $pluralModelLabel = 'خریداران';

    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return BuyerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BuyersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBuyers::route('/'),
            'create' => CreateBuyer::route('/create'),
            'edit' => EditBuyer::route('/{record}/edit'),
        ];
    }
}
