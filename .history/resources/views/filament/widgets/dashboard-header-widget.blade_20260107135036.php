<x-filament-widgets::widget>
    <x-filament::section class="bg-white dark:bg-gray-900 border-none shadow-sm rounded-2xl overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between p-4 md:p-6 gap-6 rtl">
            {{-- Right side: Profile and Notifications --}}
            <div class="flex items-center gap-6">
                {{-- Profile Section --}}
                <div
                    class="flex items-center gap-4 bg-red-50 dark:bg-red-950/30 px-6 py-3 rounded-2xl border border-red-100 dark:border-red-900/50">
                    <div class="relative">
                        <div class="w-14 h-14 rounded-full border-2 border-red-400 p-0.5 overflow-hidden">
                            <img src="{{ filament()->getUserAvatarUrl(auth()->user()) }}" alt="Profile"
                                class="w-full h-full object-cover rounded-full">
                        </div>
                    </div>
                    <div class="flex flex-col text-right">
                        <span class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="text-sm text-red-600 dark:text-red-400 font-medium">
                            {{ auth()->user()->role === \App\Models\User::ROLE_ADMIN ? 'مسؤول النظام' : 'مؤسسة' }}
                        </span>
                    </div>
                </div>

                {{-- Notification Bell --}}
                <div class="relative group">
                    <div
                        class="w-12 h-12 flex items-center justify-center bg-red-50 dark:bg-red-950/30 rounded-2xl border border-red-100 dark:border-red-900/50 transition-all group-hover:bg-red-100 dark:group-hover:bg-red-900/50 cursor-pointer">
                        <x-heroicon-o-bell class="w-6 h-6 text-red-500" />
                        {{-- Notification Badge --}}
                        <span
                            class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white border-2 border-white dark:border-gray-900">
                            2
                        </span>
                    </div>
                </div>
            </div>

            {{-- Left side: Dashboard Title and Welcome Message --}}
            <div class="flex flex-col text-center md:text-left rtl">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">
                    لوحة التحكم
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-lg">
                    مرحباً بعودتك، المشرف. إليك ما يحدث مع <span class="text-red-600 font-bold">BloodBridge</span>.
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

<style>
    .rtl {
        direction: rtl;
    }
</style>