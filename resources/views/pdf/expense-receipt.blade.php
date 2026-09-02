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

<div class="header">
        <h1>رسید هزینه</h1>
        <div class="muted">{{ $expense->organization->name }} — شماره سند: {{ $expense->id }}</div>
    </div>

    <table class="info-grid">
        <tr>
            <td class="info-label">تاریخ هزینه</td>
            <td>{{ Jalalian::fromDateTime($expense->expense_date)->format('Y/m/d') }}</td>
            <td class="info-label">دسته‌بندی</td>
            <td>{{ $expense->category?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">خریدار</td>
            <td>{{ $expense->buyer?->full_name ?? '-' }}</td>
            <td class="info-label">فروشگاه / دریافت‌کننده</td>
            <td>{{ $expense->vendor?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">روش پرداخت</td>
            <td>{{ $paymentMethodLabel }}</td>
            <td class="info-label">کارت بانکی</td>
            <td>{{ $expense->bankCard?->display_name ?? '-' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>عنوان</th>
                <th style="width: 70px;">تعداد</th>
                <th style="width: 70px;">واحد</th>
                <th style="width: 120px;">مبلغ (ریال)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expense->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->title }}</td>
                    <td class="text-center">{{ rtrim(rtrim($item->quantity, '0'), '.') ?: $item->quantity }}</td>
                    <td class="text-center">{{ $item->unit }}</td>
                    <td class="text-left">{{ number_format($item->amount) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center muted">آیتمی ثبت نشده است</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4">مجموع کل</td>
                <td class="text-left">{{ number_format($expense->total_amount) }} ریال</td>
            </tr>
        </tbody>
    </table>

    @if ($expense->notes)
        <div style="margin-top: 16px;">
            <strong>توضیحات:</strong>
            <div>{{ $expense->notes }}</div>
        </div>
    @endif

    @if ($expense->attachments->isNotEmpty())
        <div style="margin-top: 16px;">
            <strong>ضمیمه‌ها ({{ $expense->attachments->count() }}):</strong>
            <ul>
                @foreach ($expense->attachments as $attachment)
                    <li>{{ $attachment->file_name }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
