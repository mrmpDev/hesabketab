# حساب‌کتاب (hesabketab) — یادداشت‌های پروژه برای Claude

> این بخش را من (Claude) بعد از بررسی کامل کد نوشتم تا در جلسات بعدی سریع context بگیرم.
> بخش «laravel-boost-guidelines» زیر همان قوانین خودکار Laravel Boost است و دست نخورده باقی مانده — قوانین آن‌جا اولویت اجرایی دارند (pint، تست‌ها، artisan و ...).
> **هشدار مهم: این پروژه را با هیچ پروژه‌ی دیگری قاطی نکن. فقط داخل ریپوی `mrmpDev/hesabketab` کار کن.**

## این پروژه چیست؟

یک اپلیکیشن Laravel + Filament برای ثبت و مدیریت **هزینه‌های چند مطب/مجموعه** (Organization) است — چیزی شبیه یک سیستم داخلی حسابداری ساده برای هزینه‌های روزمره (خرید لوازم، پرداخت به فروشنده و ...). کل رابط کاربری فقط پنل ادمین Filament است (`/admin`)؛ فرانت‌اند عمومی وجود ندارد (`routes/web.php` فقط یک صفحه‌ی welcome پیش‌فرض Laravel دارد).

- زبان و جهت رابط: **فارسی (fa) و راست‌به‌چپ**. `config/app.php` → `locale = fa`.
- تاریخ‌ها با تقویم **جلالی/شمسی** نمایش داده می‌شوند (پکیج‌های `morilog/jalali` و `ariaieboy/filament-jalali`).
- مبالغ همیشه به‌صورت عدد صحیح (ریال) ذخیره می‌شوند، نه اعشاری.

## استک فنی

- PHP 8.2، Laravel 12، Filament v5 (Panel: فقط `admin`، در `app/Providers/Filament/AdminPanelProvider.php`)
- Livewire 4، Pest 3 برای تست (اما تقریباً هیچ تست واقعی نوشته نشده — فقط استاب پیش‌فرض)
- `spatie/laravel-permission` نصب است و روی مدل `User` تریت `HasRoles` هست، ولی **هیچ نقش/دسترسی‌ای در جایی از کد seed یا استفاده نشده** — یعنی این بخش فعلاً مرده/ناقص است.
- `leandrocfe/filament-apex-charts` نصب است ولی هیچ ویجت/چارتی در پروژه ساخته نشده (پوشه `app/Filament/Widgets` اصلاً وجود ندارد؛ فقط ویجت‌های پیش‌فرض Filament در Dashboard).
- `barryvdh/laravel-dompdf` نصب است ولی هیچ استفاده‌ای (خروجی PDF فاکتور و ...) هنوز پیاده نشده.

## مدل‌های دامنه و رابطه‌ها (`app/Models`)

```
Organization (مطب/مجموعه)
 ├─ Buyer          (خریدار)         organization_id
 ├─ BankCard       (کارت بانکی)     organization_id
 ├─ Vendor         (فروشگاه/طرف حساب) organization_id
 ├─ Expense        (هزینه)          organization_id
 └─ UserPreference                  organization_id

ExpenseCategory (دسته‌بندی هزینه) — سراسری است، دیگر organization_id ندارد
  (در مایگریشن 2026_08_10 این ستون حذف شده)

Expense
 ├─ belongsTo Organization, ExpenseCategory(as category), Buyer, BankCard, Vendor, User(as creator)
 ├─ hasMany ExpenseItem (آیتم‌های ریز خرید — عنوان/تعداد/واحد/مبلغ)
 └─ hasMany ExpenseAttachment (فایل ضمیمه)

UserPreference — آخرین انتخاب‌های کاربر (سازمان/خریدار/کارت/روش پرداخت پیش‌فرض) برای پر کردن خودکار فرم هزینه‌ی بعدی
```

payment_method یک enum است: `pos` (پوز) | `transfer` (کارت‌به‌کارت) | `cash` (نقدی). اگر pos یا transfer باشد، انتخاب `bank_card_id` اجباری می‌شود (منطق در `ExpenseForm`).

## ساختار Filament (`app/Filament/Resources/*`)

هر ریسورس این الگو را دارد (لطفاً برای ریسورس جدید همین را کپی کن):
```
{Name}Resource.php
Pages/{Create,Edit,List}{Name}.php
Schemas/{Name}Form.php   ← فرم create/edit
Tables/{Name}sTable.php  ← جدول لیست
```
ریسورس‌های فعلی: BankCards, Buyers, ExpenseCategories, Expenses, Organizations, Vendors.

نکات مهم معماری Expense form:
- انتخاب `organization_id` باعث `afterStateUpdated` می‌شود که buyer/bank_card/payment_method را از آخرین `UserPreference` کاربر پر می‌کند — ولی **در حال حاضر جایی UserPreference بعد از ثبت هزینه آپدیت/create نمی‌شود** (نه در `CreateExpense`، نه observer). یعنی این قابلیت پیش‌فرض‌سازی هوشمند عملاً همیشه خالی برمی‌گردد. اگر قرار شد این فیچر را کامل کنیم، باید در `CreateExpense::mutateFormDataBeforeCreate` یا یک Observer روی Expense، رکورد UserPreference را upsert کنیم.
- آیتم‌های هزینه (`items`) با `Repeater::make('items')->relationship()` مدیریت می‌شوند.

## باگ‌ها و ناهماهنگی‌های شناسایی‌شده

> وضعیت بعد از دور اول اصلاحات (۱۷ مرداد ۱۴۰۵ / 2026-08-17). شماره‌ها با پیام قبلی هماهنگ نگه داشته شده‌اند.

1. ✅ **رفع شد** — VendorForm/VendorsTable: مایگریشن `2026_08_17_090000_add_missing_fields_to_vendors_table.php` ستون‌های `contact_name`, `address`, `description`, `is_active` را به جدول `vendors` اضافه کرد و `fillable` مدل `Vendor` هم به‌روزرسانی شد.
2. ✅ **رفع شد** — ExpenseCategoryForm: مایگریشن `2026_08_17_090100_add_description_to_expense_categories_table.php` ستون `description` را اضافه کرد؛ همچنین یک `ColorPicker` برای فیلد `color` به فرم و یک `ColorColumn` به جدول اضافه شد.
3. ✅ **رفع شد** — فایل زائد `database/migrations/migrations.zip` حذف شد.
4. ✅ **رفع شد** — `UserPreference` حالا در `CreateExpense::afterCreate()` واقعاً upsert می‌شود. توجه: انتخاب پیش‌فرض خریدار/کارت دیگر از این جدول نمی‌آید (چون هر سازمان `is_default` خودش را دارد)؛ `UserPreference` فقط برای «آخرین سازمان استفاده‌شده توسط کاربر» در `App\Support\ExpenseDefaults::organizationId()` استفاده می‌شود.
5. ⏳ **باقی‌مانده، عمداً دست نخورده** — `spatie/laravel-permission` نصب ولی بلااستفاده است. پیاده‌سازی نقش‌ها/دسترسی‌ها نیاز به تصمیم محصولی دارد (چه نقش‌هایی؟ چه کسی به چه چیزی دسترسی دارد؟) و بدون آن ریسک شکستن دسترسی فعلی تنها ادمین را دارد؛ عمداً در این دور تغییر نکرد.
6. ⏳ **باقی‌مانده، عمداً دست نخورده** — بدون Policy برای دسترسی چندسازمانی؛ دلیل مشابه بند ۵.
7. ✅ **بخشی رفع شد** — `RefreshDatabase` در `tests/Pest.php` فعال شد و تست‌های جدید برای منطق پیش‌فرض‌ها اضافه شدند: `tests/Feature/ExpenseDefaultsTest.php` و `tests/Feature/OrganizationSingleDefaultTest.php`. پوشش کامل هنوز نیست (مثلاً تست Livewire برای خودِ صفحه‌ی `CreateExpense` نوشته نشده).
8. ✅ **رفع شد** — فکتوری برای `Organization`, `Buyer`, `BankCard`, `Vendor`, `ExpenseCategory`, `Expense`, `ExpenseItem` اضافه شد (`database/factories/*`) و `DatabaseSeeder` برای تولید داده‌ی نمونه‌ی منسجم (سازمان + خریدار/کارت پیش‌فرض + فروشنده) به‌روزرسانی شد.
9. ⏳ **باقی‌مانده، عمداً دست نخورده** — هنوز UI آپلود برای `ExpenseAttachment` در `ExpenseForm` وجود ندارد. این یک فیچر جدید (نه یک باگ رفع‌شدنی سریع) است و نیاز به تصمیم درباره‌ی storage disk و محدودیت نوع/حجم فایل دارد.

### باگ‌های تازه پیدا و رفع‌شده در همین دور

10. ✅ **اینپوت مبلغ آیتم قابل تایپ نبود** — علت: اینپوت‌های عددی با `suffix('ریال')` در چیدمان RTL فارسی، جهت پیش‌فرض input را هم RTL می‌گرفتند و همین باعث می‌شد نشانگر پشت suffix گیر کند و به نظر برسد امکان تایپ نیست (دقیقاً همان مشکلی که قبلاً برای نمایش `card_number` در `BankCardsTable` با `dir: ltr` حل شده بود). راه‌حل: `extraInputAttributes(['dir' => 'ltr', 'style' => 'text-align: left;'])` روی `total_amount`، `items.amount`، `items.quantity` (در `ExpenseForm`) و `card_number` (در `BankCardForm`) اضافه شد.
11. ✅ **انتخاب خودکار سازمان/خریدار/کارت در فرم هزینه** — منطق جدید در `App\Support\ExpenseDefaults` (کلاس مستقل و تست‌پذیر):
    - سازمان پیش‌فرض = آخرین سازمانی که کاربر استفاده کرده (از `UserPreference`)، در غیر این صورت اولین سازمان فعال (بر اساس نام).
    - خریدار پیش‌فرض = خریدار با `is_default = true` در همان سازمان.
    - کارت بانکی پیش‌فرض = کارت با `is_default = true` در همان سازمان، فقط وقتی روش پرداخت پوز/کارت‌به‌کارت باشد.
    این سه مقدار هم در بارگذاری اولیه‌ی فرم (`->default()`) و هم با تغییر سازمان یا روش پرداخت (`->afterStateUpdated()`) به‌روزرسانی می‌شوند.
12. ✅ **هر سازمان فقط یک خریدار/کارت پیش‌فرض دارد** — با یک event `saved` در مدل‌های `Buyer` و `BankCard`، وقتی رکوردی با `is_default = true` ذخیره می‌شود، بقیه‌ی رکوردهای همان سازمان به‌صورت خودکار `is_default = false` می‌شوند. این هم در فرم Filament و هم در فکتوری/seeder رعایت می‌شود.

### نکته‌ی مهم درباره‌ی محیط توسعه

در sandbox این جلسه به `packagist.org` دسترسی نداشتم، پس نتوانستم `composer install` را اجرا کنم و تست‌ها/`pint` را واقعاً ران کنم. همه‌ی فایل‌های PHP را با `php -l` سینتکس‌چک کردم و منطق را با دقت مرور کردم، ولی حتماً قبل از deploy یک بار محلی `composer install && php artisan migrate && php artisan test && vendor/bin/pint --dirty --format agent` را اجرا کن.

## قواعدی که خودم (Claude) باید رعایت کنم

- این پروژه را با هیچ پروژه‌ی دیگری (چه در حافظه، چه در artifactها) قاطی نکنم؛ همیشه مسیر واقعی ریپو (`mrmpDev/hesabketab`) را مرجع بگیرم.
- قبل از هر تغییر در فرم/جدول Filament، حتماً migration و `fillable` مدل مرتبط را چک کنم تا دوباره باگ نوع #1/#2 بالا تکرار نشود.
- متن‌های UI را فارسی و lبل‌ها را با همان لحن/سبک فایل‌های موجود بنویسم (مثلاً «مطب / مجموعه» برای Organization).
- تاریخ‌ها را همیشه با `->jalali()` (در فرم) و `Jalalian::fromDateTime(...)` یا تریت `HasJalaliDate` (در نمایش/جدول) کار کنم، نه تاریخ میلادی خام.
- برای پول همیشه integer (ریال) و بدون اعشار.
- قبل از پیشنهاد تغییر ساختار دیتابیس، به یاد داشته باشم که هر migration باید idempotent/قابل rollback باشد (مثل الگوی `fix_expense_items_table.php` که با `hasColumn` چک می‌کند).

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.2
- filament/filament (FILAMENT) - v5
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v3
- phpunit/phpunit (PHPUNIT) - v11
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
