<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;

/**
 * Small helper for working with ranges of Jalali (Shamsi) months, used by
 * the dashboard chart widgets to bucket expenses by Jalali month even
 * though `expense_date` is stored as a plain Gregorian date.
 */
class JalaliPeriod
{
    public const MONTH_NAMES = [
        1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر',
        5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان',
        9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند',
    ];

    /**
     * Gregorian [start, end] Carbon instants covering the given Jalali
     * year/month.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function monthRange(int $year, int $month): array
    {
        $start = Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/01', $year, $month))
            ->toCarbon()
            ->startOfDay();

        $nextMonth = $month + 1;
        $nextYear = $year;

        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        $end = Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/01', $nextYear, $nextMonth))
            ->toCarbon()
            ->startOfDay()
            ->subSecond();

        return [$start, $end];
    }

    /**
     * The last $count Jalali months (oldest first, current month last) as
     * [year, month] pairs.
     *
     * @return array<int, array{0: int, 1: int}>
     */
    public static function lastMonths(int $count): array
    {
        $today = Jalalian::now();
        $year = $today->getYear();
        $month = $today->getMonth();

        $months = [];

        for ($i = $count - 1; $i >= 0; $i--) {
            $m = $month - $i;
            $y = $year;

            while ($m <= 0) {
                $m += 12;
                $y--;
            }

            $months[] = [$y, $m];
        }

        return $months;
    }

    public static function monthLabel(int $month): string
    {
        return self::MONTH_NAMES[$month] ?? (string) $month;
    }
}
