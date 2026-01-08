<x-filament-widgets::widget>
    <x-filament::grid default="1" sm="2" lg="4" class="gap-6" style="direction: rtl;">
        @foreach($stats as $stat)
            <x-filament::section class="rounded-3xl shadow-sm border-none bg-white dark:bg-gray-900 group hover:shadow-md transition-all">
                <div class="flex flex-col justify-between h-32 relative">
                    {{-- Top Section --}}
                    <div class="flex items-start justify-between">
                        {{-- Text info (Right in RTL) --}}
                        <div class="flex flex-col text-right">
                            <span class="text-gray-400 dark:text-gray-500 font-bold text-lg leading-tight">{{ $stat['label'] }}</span>
                        </div>

                        {{-- Icon (Left in RTL due to flex row) --}}
                        <div class="{{ $stat['bg'] }} dark:bg-opacity-20 p-3 rounded-2xl w-14 h-14 flex items-center justify-center shrink-0">
                            <x-filament::icon
                                :icon="$stat['icon']"
                                class="w-7 h-7 {{ $stat['color'] }}"
                                style="width: 1.75rem; height: 1.75rem;"
                            />
                        </div>
                    </div>

                    {{-- Bottom Section --}}
                    <div class="flex items-end text-right">
                        <div class="text-4xl font-extrabold {{ $stat['color'] }} leading-none">
                            {{ $stat['value'] }}
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </x-filament::grid>
</x-filament-widgets::widget>