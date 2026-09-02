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

<div class="header">
    <h1>گزارش هزینه‌ها</h1>
    <div class="muted">
        {{ $organization?->name ?? 'همه‌ی مجموعه‌ها' }}
        —
        از {{ $from ? Jalalian::fromDateTime($from)->format('Y/m/d') : 'ابتدا' }}
        تا {{ $to ? Jalalian::fromDateTime($to)->format('Y/m/d') : 'انتها' }}
        —
        تعداد رکورد: {{ $expenses->count() }}
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 70px;">تاریخ</th>
            @if (! $organization)
                <th>مجموعه</th>
            @endif
            <th>دسته‌بندی</th>
            <th>خریدار</th>
            <th>فروشنده</th>
            <th style="width: 70px;">پرداخت</th>
            <th style="width: 110px;">مبلغ (ریال)</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($expenses as $expense)
            <tr>
                <td class="text-center">{{ Jalalian::fromDateTime($expense->expense_date)->format('Y/m/d') }}</td>
                @if (! $organization)
                    <td>{{ $expense->organization?->name ?? '-' }}</td>
                @endif
                <td>{{ $expense->category?->name ?? '-' }}</td>
                <td>{{ $expense->buyer?->full_name ?? '-' }}</td>
                <td>{{ $expense->vendor?->name ?? '-' }}</td>
                <td class="text-center">{{ $paymentMethodLabels[$expense->payment_method] ?? 'نامشخص' }}</td>
                <td class="text-left">{{ number_format($expense->total_amount) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $organization ? 6 : 7 }}" class="text-center muted">هزینه‌ای در این بازه یافت نشد</td>
            </tr>
        @endforelse
        @if ($expenses->isNotEmpty())
            <tr class="total-row">
                <td colspan="{{ $organization ? 5 : 6 }}">جمع کل</td>
                <td class="text-left">{{ number_format($total) }} ریال</td>
            </tr>
        @endif
    </tbody>
</table>
@endsection
