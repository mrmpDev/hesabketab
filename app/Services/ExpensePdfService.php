<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Organization;
use Illuminate\Support\Carbon;
use Mpdf\Mpdf;

class ExpensePdfService
{
    protected function makeMpdf(): Mpdf
    {
        return new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'vazirmatn',
            'directionality' => 'rtl',
            'fontDir' => [public_path('fonts/pdf')],
            'fontdata' => [
                'vazirmatn' => [
                    'R' => 'Vazirmatn-Regular.ttf',
                    'B' => 'Vazirmatn-Bold.ttf',
                ],
            ],
        ]);
    }

    public function receipt(Expense $expense): Mpdf
    {
        $expense->loadMissing(['organization', 'category', 'buyer', 'vendor', 'bankCard', 'items', 'attachments']);

        $mpdf = $this->makeMpdf();
        $mpdf->WriteHTML(view('pdf.expense-receipt', ['expense' => $expense])->render());

        return $mpdf;
    }

    public function report(?int $organizationId, ?Carbon $from, ?Carbon $to, ?array $accessibleOrganizationIds = null): Mpdf
    {
        $expenses = Expense::query()
            ->with(['organization', 'category', 'buyer', 'vendor'])
            ->when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
            ->when($accessibleOrganizationIds !== null, fn ($q) => $q->whereIn('organization_id', $accessibleOrganizationIds))
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('expense_date', '<=', $to))
            ->orderBy('expense_date')
            ->get();

        $organization = $organizationId ? Organization::find($organizationId) : null;

        $mpdf = $this->makeMpdf();
        $mpdf->WriteHTML(view('pdf.expenses-report', [
            'expenses' => $expenses,
            'organization' => $organization,
            'from' => $from,
            'to' => $to,
            'total' => $expenses->sum('total_amount'),
        ])->render());

        return $mpdf;
    }
}
