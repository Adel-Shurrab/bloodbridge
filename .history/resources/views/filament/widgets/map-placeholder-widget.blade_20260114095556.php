<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold tracking-tight">توزيع المتبرعين (قريباً)</h2>
            <div class="flex items-center space-x-2 rtl:space-x-reverse text-sm text-gray-500">
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-danger-500"></span>
                    احتياج عالي
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    متوفر
                </span>
            </div>
        </div>

        <div class="relative w-full h-80 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <!-- Placeholder Background Pattern -->
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 20px 20px;"></div>
            
            <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center">
                <div class="w-16 h-16 bg-danger-100 dark:bg-danger-900/30 rounded-full flex items-center justify-center mb-4">
                    <x-filament::icon
                        icon="heroicon-o-map"
                        class="w-8 h-8 text-danger-600 dark:text-danger-400"
                    />
                </div>
                <h3 class="text-xl font-semibold mb-2">الخريطة التفاعلية</h3>
                <p class="text-gray-500 max-w-sm">
                    سيتم قريباً ربط النظام بخرائط تساهم في توضيح الكثافة الجغرافية للمتبرعين وأماكن الاحتياج الفعلي للدم بشكل مرئي.
                </p>
                <div class="mt-6 flex gap-3">
                    <div class="px-3 py-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full text-xs font-medium shadow-sm flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-danger-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-danger-500"></span>
                        </span>
                        قيد التطوير
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
