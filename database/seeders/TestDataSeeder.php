<?php

namespace Database\Seeders;

use App\Models\BankCard;
use App\Models\Buyer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vendor;
use App\Support\JalaliPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Creates one test user per role plus a spread of sample expenses across
 * organizations and the last few months, so roles/permissions and the
 * dashboard charts can be checked manually.
 *
 * Not part of the default DatabaseSeeder — run it explicitly:
 *   php artisan db:seed --class=TestDataSeeder
 *
 * Safe to re-run: users are upserted by email, expenses are only created
 * if none exist yet for the seeded organizations/date range.
 */
class TestDataSeeder extends Seeder
{
    private const PASSWORD = 'password';

    private const PAYMENT_METHODS = ['pos', 'transfer', 'cash'];

    private const ITEM_TITLES = [
        'خرید لوازم مصرفی', 'صورتحساب ماهانه', 'هزینه حمل و نقل',
        'خرید میوه و سبزیجات', 'قبض آب و برق', 'تعمیر تجهیزات',
        'خرید لوازم اداری', 'هزینه پذیرایی',
    ];

    public function run(): void
    {
        collect(['مدیر', 'حسابدار', 'کارمند'])
            ->each(fn (string $role) => Role::findOrCreate($role, 'web'));

        $tabriz = Organization::where('code', 'tabriz-office')->first();
        $tehran = Organization::where('code', 'tehran-office')->first();
        $pakan = Organization::where('code', 'pakan-clinic')->first();

        if (! $tabriz || ! $tehran || ! $pakan) {
            $this->command?->warn('سازمان‌های پایه پیدا نشدند — ابتدا OrganizationSeeder را اجرا کنید.');

            return;
        }

        $admin = $this->upsertUser('admin.test@hesabketab.test', 'مدیر تست');
        $admin->syncRoles(['مدیر']);

        $accountant = $this->upsertUser('accountant.test@hesabketab.test', 'حسابدار تست');
        $accountant->syncRoles(['حسابدار']);
        $accountant->organizations()->sync([$tabriz->id, $tehran->id]);

        $staff = $this->upsertUser('staff.test@hesabketab.test', 'کارمند تست');
        $staff->syncRoles(['کارمند']);
        $staff->organizations()->sync([$pakan->id]);

        $this->command?->info('کاربران تست:');
        $this->command?->table(
            ['نقش', 'ایمیل', 'رمز عبور', 'دسترسی'],
            [
                ['مدیر', $admin->email, self::PASSWORD, 'همه‌ی سازمان‌ها'],
                ['حسابدار', $accountant->email, self::PASSWORD, 'مطب تبریز، مطب تهران (فقط مشاهده)'],
                ['کارمند', $staff->email, self::PASSWORD, 'کلینیک پاکان (ثبت/ویرایش)'],
            ]
        );

        foreach ([$tabriz, $tehran, $pakan] as $organization) {
            $this->seedExpensesFor($organization, $staff);
        }

        $this->command?->info('هزینه‌های نمونه برای ۶ ماه اخیر ساخته شدند.');
    }

    private function upsertUser(string $email, string $name): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make(self::PASSWORD)]
        );
    }

    private function seedExpensesFor(Organization $organization, User $creator): void
    {
        // Idempotency: skip an organization that already has seeded test
        // expenses (identified by the notes prefix below) so re-running
        // this seeder doesn't keep piling up duplicate data.
        $alreadySeeded = Expense::query()
            ->where('organization_id', $organization->id)
            ->where('notes', 'like', '[seed:test] %')
            ->exists();

        if ($alreadySeeded) {
            return;
        }

        $categories = ExpenseCategory::query()->where('is_active', true)->get();
        $vendors = Vendor::query()->where('organization_id', $organization->id)->where('is_active', true)->get();
        $buyer = Buyer::query()->where('organization_id', $organization->id)->where('is_active', true)->first();
        $bankCard = BankCard::query()->where('organization_id', $organization->id)->where('is_active', true)->first();

        if ($categories->isEmpty() || $vendors->isEmpty() || ! $buyer || ! $bankCard) {
            $this->command?->warn("داده‌های پایه (دسته/فروشنده/خریدار/کارت) برای «{$organization->name}» ناقص است، رد شد.");

            return;
        }

        foreach (JalaliPeriod::lastMonths(6) as [$year, $month]) {
            [$start, $end] = JalaliPeriod::monthRange($year, $month);

            // 3 to 6 expenses per organization per month.
            $count = random_int(3, 6);

            for ($i = 0; $i < $count; $i++) {
                $expenseDate = (clone $start)->addSeconds(random_int(0, (int) $start->diffInSeconds($end)));

                $expense = Expense::create([
                    'organization_id' => $organization->id,
                    'expense_category_id' => $categories->random()->id,
                    'buyer_id' => $buyer->id,
                    'bank_card_id' => $bankCard->id,
                    'vendor_id' => $vendors->random()->id,
                    'payment_method' => self::PAYMENT_METHODS[array_rand(self::PAYMENT_METHODS)],
                    'expense_date' => $expenseDate,
                    'total_amount' => 0,
                    'notes' => '[seed:test] داده‌ی نمونه برای تست',
                    'created_by' => $creator->id,
                ]);

                $itemCount = random_int(1, 3);
                $total = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $amount = random_int(50, 2000) * 1000; // 50,000 to 2,000,000 rials
                    $total += $amount;

                    $expense->items()->create([
                        'title' => self::ITEM_TITLES[array_rand(self::ITEM_TITLES)],
                        'quantity' => random_int(1, 5),
                        'unit' => 'عدد',
                        'amount' => $amount,
                    ]);
                }

                $expense->update(['total_amount' => $total]);
            }
        }
    }
}
