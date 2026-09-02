<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ExpensePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExpensePdfController extends Controller
{
    public function receipt(Expense $expense, ExpensePdfService $service)
    {
        abort_unless(request()->user()?->can('view', $expense), 403);

        $mpdf = $service->receipt($expense);

        return response($mpdf->Output("receipt-{$expense->id}.pdf", \Mpdf\Output\Destination::STRING_RETURN), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"receipt-{$expense->id}.pdf\"",
        ]);
    }

    public function report(Request $request, ExpensePdfService $service)
    {
        $user = $request->user();

        abort_unless($user?->can('viewAny', Expense::class), 403);

        $accessibleOrgIds = $user->isAdmin() ? null : $user->accessibleOrganizationIds();

        $organizationId = $request->integer('organization_id') ?: null;

        if ($organizationId && $accessibleOrgIds !== null && ! in_array($organizationId, $accessibleOrgIds, true)) {
            abort(403);
        }

        $from = $request->filled('from') ? Carbon::parse($request->string('from')->toString())->startOfDay() : null;
        $to = $request->filled('to') ? Carbon::parse($request->string('to')->toString())->endOfDay() : null;

        $mpdf = $service->report($organizationId, $from, $to, $accessibleOrgIds);

        return response($mpdf->Output('expenses-report.pdf', \Mpdf\Output\Destination::STRING_RETURN), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="expenses-report.pdf"',
        ]);
    }
}
