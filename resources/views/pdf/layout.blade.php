<!DOCTYPE html>
<html lang="fa" dir="rtl">
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
            direction: rtl;
            text-align: right;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        h1, h2, h3 {
            margin: 0;
        }

        /* ========================================
           HEADER
        ======================================== */

        .pdf-header {
            width: 100%;
            margin-bottom: 20px;
            padding: 15px 20px;

            /* طیف رنگی (گرادیان) ملایم طلایی تا سفید */
            background: linear-gradient(90deg, #ffffff 0%, #fdf4d6 100%);

            border: 1px solid #e6d69a;
            border-bottom: 3px solid #c9a227;
            border-radius: 8px; /* گوشه‌های گرد سربرگ */
        }

        .pdf-header-table {
            width: 100%;
            border-collapse: collapse;
            direction: ltr;
        }

        .pdf-header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .logo-cell {
            width: 100px;
            text-align: right;
            direction: rtl;
        }

        .logo {
            width: 82px;
            height: auto;
            display: inline-block;
        }

        .brand-cell {
            text-align: right;
            padding-right: 12px !important;
            direction: rtl;
        }

        .brand-name {
            font-size: 19px;
            font-weight: bold;
            color: #1a3c50;
            margin-bottom: 4px;
        }

        .brand-subtitle {
            font-size: 10px;
            color: #6b7280;
        }

        .print-info-cell {
            width: 200px;
            text-align: left;
            vertical-align: middle !important;
            direction: rtl;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
        }

        .print-date {
            font-size: 10px;
            color: #8a6b00;
            display: inline-block;
            font-weight: bold;
        }

        .print-datetime {
            font-size: 10px;
            color: #1a3c50;
            direction: ltr;
            display: inline-block;
            white-space: nowrap;
            font-weight: bold;
            margin-right: 4px;
        }

        /* ========================================
           DOCUMENT TITLE
        ======================================== */

        .document-title {
            width: 100%;
            display: table;
            margin-bottom: 16px;
            padding: 0 0 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .document-title > div {
            display: table-cell;
            vertical-align: middle;
        }

        .document-title h1 {
            font-size: 18px;
            color: #1a3c50;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .document-meta {
            font-size: 11px;
            line-height: 1.8;
            color: #4b5563;
            font-weight: bold;
        }

        .separator {
            color: #c9a227;
            padding: 0 6px;
            font-weight: bold;
        }

        /* ========================================
           SECTION TITLE
        ======================================== */

        .section-title {
            margin-top: 18px;
            margin-bottom: 10px;
            padding-right: 10px;
            border-right: 4px solid #c9a227;
            color: #1a3c50;
            font-size: 13px;
            font-weight: bold;
            background: linear-gradient(90deg, #ffffff 0%, #fdfbf7 100%);
            padding-top: 4px;
            padding-bottom: 4px;
            border-radius: 0 4px 4px 0;
        }

        /* ========================================
           INFO GRID (اطلاعات رسید)
        ======================================== */

        .info-grid {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e5e7eb;
            background-color: #fafafa;
            border-radius: 8px; /* گوشه‌های گرد کادر اطلاعات */
            overflow: hidden;
        }

        .info-grid td {
            border-bottom: 1px solid #e5e7eb;
            border-left: 1px solid #e5e7eb;
            padding: 8px 10px;
        }

        .info-grid tr:last-child td {
            border-bottom: none;
        }

        .info-grid td:last-child {
            border-left: none;
        }

        .info-label, .info-labell {
            color: #1a3c50;
            background-color: #fdfbf7;
            font-weight: bold;
            width: 115px;
            font-size: 10.5px;
        }

        .info-value {
            color: #1f2937;
            font-size: 11px;
        }

        /* ========================================
           TABLES (جدول اصلی هزینه‌ها / گزارش)
        ======================================== */

        .items-table {
            width: 100%;
            border-collapse: separate; /* حیاتی برای کارکرد border-radius */
            border-spacing: 0;
            margin-top: 6px;
            border-radius: 8px; /* گوشه‌های گرد کل جدول */
            overflow: hidden;
            border: 1px solid #dfe3e6;
        }

        .items-table th,
        .items-table td {
            border-bottom: 1px solid #dfe3e6;
            border-left: 1px solid #dfe3e6;
            padding: 8px 10px;
            text-align: right;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            border-left: none;
        }

        .items-table th {
            background-color: #1a3c50;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            padding-top: 10px;
            padding-bottom: 10px;
            border-bottom: 3px solid #c9a227;
        }

        /* گرد کردن گوشه‌های بالای هدر جدول */
        .items-table thead tr:first-child th:first-child {
            border-top-right-radius: 7px;
        }

        .items-table thead tr:first-child th:last-child {
            border-top-left-radius: 7px;
        }

        .items-table tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        .items-table tbody tr:nth-child(odd) td {
            background-color: #ffffff;
        }

        .items-table td {
            font-size: 11px;
        }

        .report-table th {
            font-size: 10px;
        }

        /* ========================================
           ALIGNMENT
        ======================================== */

        .text-left {
            text-align: left !important;
        }

        .text-center {
            text-align: center !important;
        }

        /* ========================================
           TOTAL (سطر جمع کل)
        ======================================== */

        .total-row td {
            background-color: #fdfbf7 !important;
            color: #1a3c50;
            font-weight: bold;
            border-top: 2px solid #c9a227;
            border-bottom: none !important;
            padding-top: 12px;
            padding-bottom: 12px;
        }

        /* گرد کردن گوشه‌های پایین سطر آخر جدول */
        .items-table tbody tr:last-child td:first-child,
        .total-row td:first-child {
            border-bottom-right-radius: 7px;
        }

        .items-table tbody tr:last-child td:last-child,
        .total-row td:last-child {
            border-bottom-left-radius: 7px;
        }

        .total-amount {
            color: #1a3c50 !important;
            font-size: 12px;
            direction: rtl;
            text-align: left !important;
            white-space: nowrap;
        }

        .currency-prefix {
            display: inline-block;
            direction: rtl;
            font-size: 10px;
            margin-left: 6px;
            color: #6b7280;
        }

        .total-number {
            display: inline-block;
            direction: ltr;
        }

        /* ========================================
           SUMMARY BAR (کادر خلاصه بالای جدول)
        ======================================== */

        .summary-bar-table {
            width: 100%;
            margin-bottom: 16px;
            background-color: #fdfbf7;
            border-collapse: separate; /* حیاتی برای border-radius */
            border-spacing: 0;
            border: 1px solid #e6d69a;
            border-radius: 8px; /* گوشه‌های گرد کادر خلاصه */
            overflow: hidden;
        }

        .summary-bar-table td {
            width: 50%;
            padding: 12px 25px !important;
            vertical-align: middle;
            text-align: right;
            border-left: 1px solid #e6d69a;
        }

        .summary-bar-table td:last-child {
            border-left: none;
        }

        .summary-label {
            color: #8a6b00;
            font-size: 10.5px;
            margin-left: 8px;
            font-weight: bold;
        }

        /* ========================================
           CONTENT BOX
        ======================================== */

        .content-box {
            margin-top: 18px;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            padding: 12px 14px;
            border-radius: 8px; /* گوشه‌های گرد کادرهای توضیحات */
        }

        .box-title {
            color: #1a3c50;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            border-bottom: 1px dashed #d1d5db;
            padding-bottom: 5px;
        }

        .box-content {
            color: #4b5563;
            line-height: 1.8;
            font-size: 11px;
        }

        /* ========================================
           EMPTY STATE
        ======================================== */

        .empty-row {
            padding-top: 20px !important;
            padding-bottom: 20px !important;
        }

        .muted {
            color: #6b7280;
        }

        /* ========================================
           FOOTER
        ======================================== */

        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }

        @media print {
            .items-table thead { display: table-header-group; }
            .items-table tr { page-break-inside: avoid; }
            .content-box { page-break-inside: avoid; }
        }
    </style>
</head>

<body>

<div class="pdf-header">
    <table class="pdf-header-table">
        <tr>
            <td class="print-info-cell">
                <span class="print-date">@fa('تاریخ چاپ:') </span>
                <span class="print-datetime">{{ \Morilog\Jalali\Jalalian::now()->format('Y/m/d H:i') }}</span>
            </td>

            <td class="brand-cell">
                <div class="brand-name">
                    @fa('حساب‌کتاب')
                </div>
                <div class="brand-subtitle">
                    @fa('سیستم مدیریت مالی و حسابداری')
                </div>
            </td>

            <td class="logo-cell">
                <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Logo">
            </td>
        </tr>
    </table>
</div>

@yield('content')

<div class="footer">
    @fa('تولید شده توسط سیستم حساب‌کتاب')
    —
    {{ \Morilog\Jalali\Jalalian::now()->format('Y/m/d H:i') }}
</div>

</body>
</html>
