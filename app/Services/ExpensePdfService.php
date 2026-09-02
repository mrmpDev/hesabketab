<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Organization;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfDocument;
use Illuminate\Support\Carbon;

class ExpensePdfService
{
    public function receipt(Expense $expense): PdfDocument
    {
        $expense->loadMissing(['organization', 'category', 'buyer', 'vendor', 'bankCard', 'items', 'attachments']);

        return Pdf::loadView('pdf.expense-receipt', ['expense' => $expense])
            ->setPaper('a4', 'portrait');
    }

    /**
     * @param  array<int, int>|null  $accessibleOrganizationIds  Null means unrestricted (admin).
     */
    public function report(
        ?int $organizationId,
        ?Carbon $from,
        ?Carbon $to,
        ?array $accessibleOrganizationIds = null
    ): PdfDocument {
        $expenses = Expense::query()
            ->with(['organization', 'category', 'buyer', 'vendor'])
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId))
            ->when(
                $accessibleOrganizationIds !== null,
                fn ($query) => $query->whereIn('organization_id', $accessibleOrganizationIds)
            )
            ->when($from, fn ($query) => $query->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('expense_date', '<=', $to))
            ->orderBy('expense_date')
            ->get();

        $organization = $organizationId ? Organization::find($organizationId) : null;

        return Pdf::loadView('pdf.expenses-report', [
            'expenses' => $expenses,
            'organization' => $organization,
            'from' => $from,
            'to' => $to,
            'total' => $expenses->sum('total_amount'),
        ])->setPaper('a4', 'portrait');
    }
}
