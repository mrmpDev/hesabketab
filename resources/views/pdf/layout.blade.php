{{--<!DOCTYPE html>--}}
{{--<html lang="fa" dir="rtl">--}}
{{--<head>--}}
{{--    <meta charset="utf-8">--}}
{{--    <title>{{ $title ?? 'حساب‌کتاب' }}</title>--}}
{{--    <style>--}}
{{--        @font-face {--}}
{{--            font-family: 'Vazirmatn';--}}
{{--            src: url('{{ public_path('fonts/pdf/Vazirmatn-Regular.ttf') }}');--}}
{{--            font-weight: normal;--}}
{{--            font-style: normal;--}}
{{--        }--}}

{{--        @font-face {--}}
{{--            font-family: 'Vazirmatn';--}}
{{--            src: url('{{ public_path('fonts/pdf/Vazirmatn-Bold.ttf') }}');--}}
{{--            font-weight: bold;--}}
{{--            font-style: normal;--}}
{{--        }--}}

{{--        * {--}}
{{--            box-sizing: border-box;--}}
{{--        }--}}

{{--        body {--}}
{{--            font-family: 'Vazirmatn', sans-serif;--}}
{{--            direction: rtl;--}}
{{--            text-align: right;--}}
{{--            font-size: 12px;--}}
{{--            color: #1f2937;--}}
{{--        }--}}

{{--        h1, h2, h3 {--}}
{{--            margin: 0 0 8px 0;--}}
{{--        }--}}

{{--        table {--}}
{{--            width: 100%;--}}
{{--            border-collapse: collapse;--}}
{{--        }--}}

{{--        th, td {--}}
{{--            border: 1px solid #d1d5db;--}}
{{--            padding: 6px 8px;--}}
{{--            text-align: right;--}}
{{--        }--}}

{{--        th {--}}
{{--            background-color: #f3f4f6;--}}
{{--            font-weight: bold;--}}
{{--        }--}}

{{--        .text-left {--}}
{{--            text-align: left;--}}
{{--        }--}}

{{--        .text-center {--}}
{{--            text-align: center;--}}
{{--        }--}}

{{--        .muted {--}}
{{--            color: #6b7280;--}}
{{--        }--}}

{{--        .header {--}}
{{--            border-bottom: 2px solid #ec4899;--}}
{{--            padding-bottom: 10px;--}}
{{--            margin-bottom: 16px;--}}
{{--        }--}}

{{--        .footer {--}}
{{--            margin-top: 24px;--}}
{{--            padding-top: 8px;--}}
{{--            border-top: 1px solid #d1d5db;--}}
{{--            font-size: 10px;--}}
{{--            color: #9ca3af;--}}
{{--        }--}}

{{--        .info-grid {--}}
{{--            width: 100%;--}}
{{--            margin-bottom: 16px;--}}
{{--        }--}}

{{--        .info-grid td {--}}
{{--            border: none;--}}
{{--            padding: 4px 0;--}}
{{--        }--}}

{{--        .info-label {--}}
{{--            color: #6b7280;--}}
{{--            width: 120px;--}}
{{--        }--}}

{{--        .total-row td {--}}
{{--            font-weight: bold;--}}
{{--            background-color: #fdf2f8;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}
{{--<body>--}}
{{--    @yield('content')--}}

{{--    <div class="footer">--}}
{{--        تولید شده توسط سیستم حساب‌کتاب — {{ \Morilog\Jalali\Jalalian::now()->format('Y/m/d H:i') }}--}}
{{--    </div>--}}
{{--</body>--}}
{{--</html>--}}


    <!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'حساب‌کتاب' }}</title>
    <style>
        @font-face {
            font-family: 'Vazirmatn';
            src: url('{{ public_path('fonts/pdf/Vazirmatn-Regular.ttf') }}');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Vazirmatn';
            src: url('{{ public_path('fonts/pdf/Vazirmatn-Bold.ttf') }}');
            font-weight: bold;
            font-style: normal;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Vazirmatn', sans-serif;
            text-align: right;
            font-size: 12px;
            color: #1f2937;
        }

        h1, h2, h3 {
            margin: 0 0 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: right;
        }

        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #6b7280;
        }

        .header {
            border-bottom: 2px solid #ec4899;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .footer {
            margin-top: 24px;
            padding-top: 8px;
            border-top: 1px solid #d1d5db;
            font-size: 10px;
            color: #9ca3af;
        }

        .info-grid {
            width: 100%;
            margin-bottom: 16px;
        }

        .info-grid td {
            border: none;
            padding: 4px 0;
        }

        .info-label {
            color: #6b7280;
            width: 120px;
        }

        .total-row td {
            font-weight: bold;
            background-color: #fdf2f8;
        }
    </style>
</head>
<body>
@yield('content')

<div class="footer">
    تولید شده توسط سیستم حساب‌کتاب — {{ \Morilog\Jalali\Jalalian::now()->format('Y/m/d H:i') }}
</div>
</body>
</html>
