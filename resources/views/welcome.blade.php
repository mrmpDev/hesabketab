<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>حساب کتاب - سیستم مدیریت مالی و حسابداری</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased dark:bg-slate-900 dark:text-slate-100 min-h-screen flex flex-col justify-between">

<!-- هدر سایت -->
<header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo.png') }}" alt="لوگوی حساب کتاب" class="w-10 h-10 object-contain">
        <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">حساب کتاب</span>
    </div>

    @if (Route::has('login'))
        <nav class="flex items-center gap-4">
            @auth
                <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-medium bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">داشبورد</a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-pink-600 dark:hover:text-pink-400 transition">ورود</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition shadow-sm">ثبت‌نام</a>
                @endif
            @endauth
        </nav>
    @endif
</header>

<!-- بخش اصلی محتوا -->
<main class="flex-grow flex items-center justify-center px-6 py-12">
    <div class="max-w-3xl mx-auto text-center">

        <!-- لوگوی بزرگ در مرکز -->
        <div class="mb-8 flex justify-center">
            <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700">
                <img src="{{ asset('images/logo.png') }}" alt="حساب کتاب" class="w-24 h-24 object-contain mx-auto">
            </div>
        </div>

        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-6">
            مدیریت مالی سازمان شما با <span class="text-pink-600 dark:text-pink-400">حساب کتاب</span>
        </h1>

        <p class="text-lg text-slate-600 dark:text-slate-400 mb-6 max-w-2xl mx-auto leading-relaxed">
            سیستم جامع حسابداری و مدیریت مالی «حساب کتاب»، ابزاری دقیق و مطمئن برای ثبت تراکنش‌ها، پیگیری هزینه‌ها و کنترل کامل وضعیت مالی کسب‌وکار شما.
        </p>

        <!-- دکمه ورود به پنل ادمین -->
        <div class="mb-10">
            <a href="https://hesabketab.test/admin" class="px-8 py-3.5 bg-pink-600 hover:bg-pink-700 text-white font-medium rounded-xl shadow-lg shadow-pink-600/25 transition inline-block">ورود به پنل ادمین</a>
        </div>

        <!-- دکمه‌های عملیاتی -->
        <div class="flex flex-wrap items-center justify-center gap-4">
            @auth
                <a href="{{ url('/admin') }}" class="px-8 py-3.5 bg-pink-600 hover:bg-pink-700 text-white font-medium rounded-xl shadow-lg shadow-pink-600/20 transition">ورود به پنل کاربری</a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="px-8 py-3.5 bg-pink-600 hover:bg-pink-700 text-white font-medium rounded-xl shadow-lg shadow-pink-600/20 transition">شروع کنید</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-8 py-3.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-medium rounded-xl border border-slate-300 dark:border-slate-700 transition">ایجاد حساب کاربری</a>
                @endif
            @endauth
        </div>

    </div>
</main>

<!-- فوتر -->
<footer class="w-full max-w-7xl mx-auto px-6 py-6 text-center text-sm text-slate-500 dark:text-slate-400 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
    <p>تمامی حقوق محفوظ است &copy; {{ date('Y') }} حساب کتاب</p>
    <p class="flex items-center justify-center gap-2 flex-wrap">
        <span>طراحی‌شده با فنجان‌های چای پیاپی و شکلات</span>
        <svg class="w-4 h-4 text-pink-600 dark:text-pink-400 inline-block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
            <line x1="6" y1="1" x2="6" y2="4"></line>
            <line x1="10" y1="1" x2="10" y2="4"></line>
            <line x1="14" y1="1" x2="14" y2="4"></line>
        </svg>
        <span class="text-slate-300 dark:text-slate-700">|</span>
        <span>توسعه داده شده توسط <a href="https://mrmp.ir" target="_blank" class="text-pink-600 dark:text-pink-400 hover:underline font-medium">mrmp</a></span>
    </p>
</footer>

</body>
</html>
