<x-filament-widgets::widget>
    <x-filament::section style="background-color: light-dark(#EC4899, rgba(236, 72, 153, 1));">
        <div
            x-data="{ time: '' }"
            x-init="
                const update = () => {
                    time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                };
                update();
                setInterval(update, 1000);
            "

            class="flex flex-col items-center justify-center gap-2"
        >
            <span class="text-3xl font-bold tracking-tight" x-text="time"></span>

            <div class="flex flex-col items-center gap-0.5">
                <span class="block text-sm text-gray-600 dark:text-gray-300">
                    {{ $jalaliDate }}
                </span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>


