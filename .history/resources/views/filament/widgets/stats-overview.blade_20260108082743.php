<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 rtl">
        @foreach($stats as $stat)
            <div
                class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 shadow-sm border border-gray-100 dark:border-gray-800 flex flex-col justify-between h-48 relative overflow-hidden group hover:shadow-md transition-all">
                {{-- Top Section --}}
                <div class="flex items-start justify-between">
                    {{-- Text info (Left in RTL) --}}
                    <div class="flex flex-col text-right">
                        <span class="text-gray-400 dark:text-gray-500 font-bold text-lg mb-1">{{ $stat['label'] }}</span>
                        {{-- <span class="text-xs text-gray-300 dark:text-gray-600 font-medium">{{ $stat['desc'] }}</span>
                        --}}
                    </div>

                    {{-- Icon (Right in RTL) --}}
                    <div class="{{ $stat['bg'] }} dark:bg-opacity-20 p-4 rounded-2xl w-16 h-16 flex items-center justify-center shrink-0">
                        <x-filament::icon
                            :icon="$stat['icon']"
                            class="w-8 h-8 {{ $stat['color'] }}"
                            style="width: 2rem; height: 2rem;"
                        />
                    </div>
                </div>

                {{-- Bottom Section --}}
                <div class="flex items-end justify-between">
                    <div class="text-[3rem] font-black {{ $stat['color'] }} leading-none">
                        {{ $stat['value'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>

<style>
    .rtl {
        direction: rtl;
    }
</style>