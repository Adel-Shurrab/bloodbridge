<x-filament-widgets::widget>
    <div class="fi-wi-stats-overview-adapter">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" style="direction: rtl;">
            @foreach($stats as $stat)
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6 flex flex-col justify-between h-40 group hover:shadow-md transition-all border-none">
                    {{-- Top Section --}}
                    <div class="flex items-start justify-between">
                        {{-- Text info (Right in RTL) --}}
                        <div class="flex flex-col text-right">
                            <span class="text-gray-500 dark:text-gray-400 font-bold text-lg leading-tight">{{ $stat['label'] }}</span>
                        </div>

                        {{-- Icon (Left in RTL) --}}
                        <div class="{{ $stat['bg'] }} dark:bg-opacity-20 p-3 rounded-xl flex items-center justify-center shrink-0" style="width: 3.5rem; height: 3.5rem;">
                            <x-filament::icon
                                :icon="$stat['icon']"
                                class="w-8 h-8 {{ $stat['color'] }}"
                                style="width: 2rem; height: 2rem;"
                            />
                        </div>
                    </div>

                    {{-- Bottom Section --}}
                    <div class="flex items-end text-right">
                        <div class="text-5xl font-black {{ $stat['color'] }} leading-none tracking-tight">
                            {{ $stat['value'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>