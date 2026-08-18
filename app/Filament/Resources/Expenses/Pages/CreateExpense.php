<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use App\Models\UserPreference;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    /**
     * Remember this user's latest choices per organization so that the
     * "last used organization" default (see App\Support\ExpenseDefaults)
     * keeps working across visits.
     */
    protected function afterCreate(): void
    {
        UserPreference::query()->updateOrCreate(
            [
                'user_id' => auth()->id(),
                'organization_id' => $this->record->organization_id,
            ],
            [
                'buyer_id' => $this->record->buyer_id,
                'bank_card_id' => $this->record->bank_card_id,
                'default_payment_method' => $this->record->payment_method,
            ]
        );
    }

    protected function getRedirectUrl(): string
    {
        return ExpenseResource::getUrl('index');
    }
}
