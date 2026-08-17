<?php

namespace App\Filament\Resources\BankCards\Pages;

use App\Filament\Resources\BankCards\BankCardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankCard extends CreateRecord
{
    protected static string $resource = BankCardResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
