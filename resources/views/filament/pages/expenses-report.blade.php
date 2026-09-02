<x-filament-panels::page>
    <x-filament::section>
        <form method="GET" action="{{ route('expenses.report.pdf') }}" target="_blank" class="grid gap-6 max-w-xl">
            <div class="grid gap-2">
                <label for="report-organization" class="text-sm font-medium text-gray-950 dark:text-white">
                    مطب / مجموعه
                </label>
                <x-filament::input.wrapper>
                    <x-filament::input.select id="report-organization" name="organization_id">
                        <option value="">همه‌ی مجموعه‌های در دسترس</option>
                        @foreach ($this->getOrganizations() as $organization)
                            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="grid gap-2">
                    <label for="report-from" class="text-sm font-medium text-gray-950 dark:text-white">
                        از تاریخ
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" id="report-from" name="from" />
                    </x-filament::input.wrapper>
                </div>

                <div class="grid gap-2">
                    <label for="report-to" class="text-sm font-medium text-gray-950 dark:text-white">
                        تا تاریخ
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" id="report-to" name="to" />
                    </x-filament::input.wrapper>
                </div>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400">
                تاریخ‌ها بر اساس تقویم میلادی مرورگر انتخاب می‌شوند؛ گزارش نهایی با تاریخ شمسی نمایش داده خواهد شد.
                در صورت خالی گذاشتن هر دو تاریخ، تمام هزینه‌های ثبت‌شده در گزارش می‌آید.
            </p>

            <div>
                <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
                    دانلود / نمایش PDF
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament-panels::page>
