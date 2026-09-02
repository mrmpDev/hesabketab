<x-filament-panels::page>
    <x-filament::section>
        <form wire:submit="generateReport" class="flex flex-col gap-6 max-w-xl">

            {{-- رندر خودکار فرم توسط فیلامنت --}}
            {{ $this->form }}

            {{-- فاصله گرفتن متن راهنما از فیلدها --}}

               <p style="margin-top: 20px; line-height: 35px; text-align: center">
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
