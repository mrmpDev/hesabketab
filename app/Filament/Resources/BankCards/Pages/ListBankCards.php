<?php

namespace App\Filament\Resources\BankCards\Pages;

use App\Filament\Resources\BankCards\BankCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBankCards extends ListRecords
{
    protected static string $resource = BankCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('کارت جدید'),
        ];
    }
}
