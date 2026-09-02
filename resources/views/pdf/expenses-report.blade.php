@extends('pdf.layout', ['title' => 'گزارش هزینه‌ها'])

@section('content')
    @php
        use Morilog\Jalali\Jalalian;

        $paymentMethodLabels = [
            'pos' => 'پوز',
            'transfer' => 'کارت‌به‌کارت',
            'cash' => 'نقدی',
        ];
    @endphp

    <div class="document-title">
        <div>
            <h1>@fa('گزارش هزینه‌ها')</h1>

            <div class="document-meta">
                @fa($organization?->name ?? 'همه‌ی مجموعه‌ها')
                <span class="separator">|</span>
                @fa('از '.($from ? Jalalian::fromDateTime($from)->format('Y/m/d') : 'ابتدا'))
                <span class="separator">|</span>
                @fa('تا '.($to ? Jalalian::fromDateTime($to)->format('Y/m/d') : 'انتها'))
            </div>
        </div>
    </div>

    <table class="summary-bar-table">
        <tr>
            <td>
                <span class="summary-label">@fa('تعداد رکورد:')</span>
                <strong>{{ $expenses->count() }}</strong>
            </td>

            @if ($expenses->isNotEmpty())
                <td>
                    <span class="summary-label">@fa('جمع کل:')</span>
                    <strong>
                        <span class="total-number">{{ number_format($total) }}</span>
                        <span class="currency">@fa('ریال')</span>
                    </strong>
                </td>
            @endif
        </tr>
    </table>

    <table class="items-table report-table">
        <thead>
        <tr>
            <th style="width: 75px;">@fa('تاریخ')</th>

            @if (! $organization)
                <th>@fa('مجموعه')</th>
            @endif

            <th>@fa('دسته‌بندی')</th>
            <th>@fa('خریدار')</th>
            <th>@fa('فروشنده')</th>
            <th style="width: 75px;">@fa('پرداخت')</th>
            <th style="width: 120px;">@fa('مبلغ (ریال)')</th>
        </tr>
        </thead>

        <tbody>
        @forelse ($expenses as $expense)
            <tr>
                <td class="text-center">
                    {{ Jalalian::fromDateTime($expense->expense_date)->format('Y/m/d') }}
                </td>

                @if (! $organization)
                    <td>
                        @fa($expense->organization?->name ?? '-')
                    </td>
                @endif

                <td>
                    @fa($expense->category?->name ?? '-')
                </td>

                <td>
                    @fa($expense->buyer?->full_name ?? '-')
                </td>

                <td>
                    @fa($expense->vendor?->name ?? '-')
                </td>

                <td class="text-center">
                    @fa($paymentMethodLabels[$expense->payment_method] ?? 'نامشخص')
                </td>

                <td class="text-left amount" style="direction: rtl;">
                    <span class="total-number">{{ number_format($expense->total_amount) }}</span>
                </td>
            </tr>
        @empty
            <tr>
                <td
                    colspan="{{ $organization ? 6 : 7 }}"
                    class="text-center muted empty-row"
                >
                    @fa('هزینه‌ای در این بازه یافت نشد')
                </td>
            </tr>
        @endforelse

        @if ($expenses->isNotEmpty())
            <tr class="total-row">
                <td colspan="{{ $organization ? 5 : 6 }}">
                    @fa('جمع کل')
                </td>

                <td class="amount total-amount">
                    <span class="total-number">{{ number_format($total) }}</span>
                    <span class="currency-prefix">@fa('ریال')</span>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
@endsection
