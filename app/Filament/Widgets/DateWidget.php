<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Morilog\Jalali\Jalalian;

class DateWidget extends Widget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = [
        'default' => 'full', // در موبایل کل عرض صفحه را می‌گیرد
        'md' => 1,           // در تبلت و دسکتاپ فقط 1 ستون اشغال می‌کند
    ];

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.date-widget';


    public function getViewData(): array
    {
        $jalali = Jalalian::now();

        $jalaliDate = sprintf(
            'امروز %s %d %s ماه سال %d',
            $jalali->format('%A'),   // نام روز هفته (مثلاً شنبه)
            $jalali->getDay(),       // عدد روز بدون صفر اضافه (مثلاً 3)
            $jalali->format('%B'),   // نام ماه (مثلاً مرداد)
            $jalali->getYear()       // سال (مثلاً 1405)
        );

        return [
            'jalaliDate' => $jalaliDate,
        ];
    }
}
