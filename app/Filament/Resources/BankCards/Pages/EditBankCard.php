<?php

namespace App\Filament\Resources\BankCards\Pages;

use App\Filament\Resources\BankCards\BankCardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBankCard extends EditRecord
{
    protected static string $resource = BankCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('حذف'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
