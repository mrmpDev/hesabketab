<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExpense extends EditRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printReceipt')
                ->label('چاپ رسید')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('expenses.receipt.pdf', $this->record))
                ->openUrlInNewTab(),

            DeleteAction::make()
                ->label('حذف'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return ExpenseResource::getUrl('index');
    }
}
