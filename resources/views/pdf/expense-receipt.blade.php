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

            <td class="info-label">@fa('فروشگاه / دریافت‌کننده')</td>
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
        <div class="content-box" style="margin-bottom: 0;">
            <div class="box-title">
                @fa('توضیحات')
            </div>

            <div class="box-content">
                @fa($expense->notes)
            </div>
        </div>
    @endif

    @if ($expense->attachments->isNotEmpty())
        <style>
            .attachments-box {
                margin-top: 15px;
                page-break-before: avoid;
                page-break-inside: avoid;
            }

            .attachment-table {
                width: 100%;
                table-layout: fixed;
                border-collapse: collapse;
            }

            .attachment-td {
                width: 33.333%;
                padding: 6px;
                vertical-align: top;
            }

            .attachment-card {
                background-color: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 6px;
                box-sizing: border-box;
            }

            /* اعمال ابعاد ثابت و یکسان برای تمامی تصاویر */
            .attachment-card img {
                width: 100% !important;
                height: 160px !important; /* ارتفاع کاملاً ثابت و یکدست برای همه عکس‌ها */
                object-fit: cover !important; /* برش هوشمند تصویر برای جلوگیری از کشیدگی و دفرمه شدن */
                border-radius: 4px;
                display: block;
            }
        </style>

        <div class="content-box attachments-box">
            <div class="box-title">
                @fa('ضمیمه‌ها')
                <span class="attachment-count" style="font-size: 0.9em; color: #64748b;">
                    ({{ $expense->attachments->count() }})
                </span>
            </div>

            <table class="attachment-table" dir="rtl">
                @foreach ($expense->attachments->chunk(3) as $chunk)
                    <tr>
                        @foreach ($chunk as $attachment)
                            <td class="attachment-td">
                                <div class="attachment-card">
                                    <img  style="width: 280px; height: 370px; border: 2px solid #c9a227;" src="{{ Storage::url("expense-attachments/$attachment->file_name") }}" alt="ضمیمه سند">
                                </div>
                            </td>
                        @endforeach

                        @for ($i = $chunk->count(); $i < 3; $i++)
                            <td class="attachment-td"></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
@endsection
