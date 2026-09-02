@extends('pdf.layout', ['title' => 'رسید هزینه شماره '.$expense->id])

@section('content')
    @php
        use Morilog\Jalali\Jalalian;

        $paymentMethodLabel = match ($expense->payment_method) {
            'pos' => 'پوز',
            'transfer' => 'کارت‌به‌کارت',
            'cash' => 'نقدی',
            default => 'نامشخص',
        };
    @endphp

    <div class="document-title">
        <div>
            <h1>@fa('رسید هزینه')</h1>

            <div class="document-meta">
                @fa($expense->organization?->name ?? '-')
                <span class="separator">|</span>
                @fa('شماره سند: '.$expense->id)
            </div>
        </div>
    </div>

    <div class="section-title">
        @fa('اطلاعات هزینه')
    </div>

    <table class="info-grid">
        <tr>
            <td class="info-label">@fa('تاریخ هزینه')</td>
            <td class="info-value">
                {{ Jalalian::fromDateTime($expense->expense_date)->format('Y/m/d') }}
            </td>

            <td class="info-label">@fa('دسته‌بندی')</td>
            <td class="info-value">
                @fa($expense->category?->name ?? '-')
            </td>
        </tr>

        <tr>
            <td class="info-label">@fa('خریدار')</td>
            <td class="info-value">
                @fa($expense->buyer?->full_name ?? '-')
            </td>

            <td class="info-labell">@fa('فروشگاه / دریافت‌کننده')</td>
            <td class="info-value">
                @fa($expense->vendor?->name ?? '-')
            </td>
        </tr>

        <tr>
            <td class="info-label">@fa('روش پرداخت')</td>
            <td class="info-value">
                @fa($paymentMethodLabel)
            </td>

            <td class="info-label">@fa('کارت بانکی')</td>
            <td class="info-value">
                @fa($expense->bankCard?->display_name ?? '-')
            </td>
        </tr>
    </table>

    <div class="section-title">
        @fa('جزئیات هزینه')
    </div>

    <table class="items-table">
        <thead>
        <tr>
            <th style="width: 35px;">#</th>
            <th>@fa('عنوان')</th>
            <th style="width: 70px;">@fa('تعداد')</th>
            <th style="width: 70px;">@fa('واحد')</th>
            <th style="width: 125px;">@fa('مبلغ (ریال)')</th>
        </tr>
        </thead>

        <tbody>
        @forelse ($expense->items as $index => $item)
            <tr>
                <td class="text-center">
                    {{ $index + 1 }}
                </td>

                <td>
                    @fa($item->title)
                </td>

                <td class="text-center">
                    {{ rtrim(rtrim($item->quantity, '0'), '.') ?: $item->quantity }}
                </td>

                <td class="text-center">
                    @fa($item->unit)
                </td>

                <td class="text-left amount" style="direction: rtl;">
                    <span class="total-number">{{ number_format($item->amount) }}</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center muted">
                    @fa('آیتمی ثبت نشده است')
                </td>
            </tr>
        @endforelse

        <tr class="total-row">
            <td colspan="4">
                @fa('مجموع کل')
            </td>

            <td class="amount total-amount">
                <span class="total-number">{{ number_format($expense->total_amount) }}</span>
                <span class="currency-prefix">@fa('ریال')</span>
            </td>
        </tr>
        </tbody>
    </table>

    @if ($expense->notes)
        <div class="content-box">
            <div class="box-title">
                @fa('توضیحات')
            </div>

            <div class="box-content">
                @fa($expense->notes)
            </div>
        </div>
    @endif

    @if ($expense->attachments->isNotEmpty())
        <div class="content-box attachments-box">
            <div class="box-title">
                @fa('ضمیمه‌ها')
                <span class="attachment-count">
                    ({{ $expense->attachments->count() }})
                </span>
            </div>

            <ul class="attachments-list">
                @foreach ($expense->attachments as $attachment)
                    <li>
                        @fa($attachment->file_name)
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
