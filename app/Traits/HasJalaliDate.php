<?php

namespace App\Traits;

use Morilog\Jalali\Jalalian;

trait HasJalaliDate
{
    public function toJalali($date, string $format = 'Y/m/d'): ?string
    {
        return $date
            ? Jalalian::fromDateTime($date)->format($format)
            : null;
    }
}
