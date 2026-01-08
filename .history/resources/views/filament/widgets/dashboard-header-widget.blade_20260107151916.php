<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-gray-800 rounded-[2rem] overflow-hidden p-6 md:p-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8 rtl">
            
            {{-- Right side: Dashboard Title and Welcome Message --}}
            <div class="flex flex-col text-right order-1 md:order-2">
                <h1 class="text-[2.5rem] font-black text-gray-800 dark:text-white mb-2 leading-none tracking-tight">
                    لوحة التحكم
                </h1>
                <p class="text-gray-400 dark:text-gray-500 text-xl font-medium">
                    مرحباً بعودتك، المشرف. إليك ما يحدث مع <span class="text-blue-600/80 font-bold">BloodBridge</span>.
                </p>
            </div>

            {{-- Left side: Profile and Notifications --}}
            <div class="flex items-center gap-5 order-2 md:order-1">
                {{-- Profile Section --}}
                <div class="flex items-center gap-4 bg-[#FFF5F5] dark:bg-red-950/20 px-6 py-4 rounded-[2rem] border border-red-50 dark:border-red-900/10">
                    <div class="flex flex-col text-right">
                        <span class="text-xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">
                            {{ auth()->user()->role === \App\Models\User::ROLE_ADMIN ? 'مسؤول النظام' : 'مؤسسة' }}
                        </span>
                    </div>
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full border-[3px] border-white dark:border-gray-800 shadow-md overflow-hidden ring-4 ring-red-100/50 dark:ring-red-900/20">
                            <img src="{{ filament()->getUserAvatarUrl(auth()->user()) }}" alt="Profile" class="w-full h-full object-cover rounded-full bg-white">
                        </div>
                    </div>
                </div>

                {{-- Notification Bell (Functional) --}}
                @php
                    $unreadCount = auth()->user()->unreadNotifications()->count();
                @endphp
                <div class="relative">
                    <button
                        x-on:click="$dispatch('open-database-notifications')"
                        type="button"
                        class="w-[4.5rem] h-[3.5rem] flex items-center justify-center bg-[#FFF5F5] dark:bg-red-950/20 rounded-[1.5rem] border border-red-50 dark:border-red-900/10 transition-all hover:scale-105 active:scale-95 cursor-pointer focus:outline-none relative shadow-sm"
                    >
                        <x-heroicon-s-bell class="w-8 h-8 text-[#FFBABA]" />
                        
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-[11px] font-bold text-white border-[3px] border-white dark:border-gray-900 shadow-sm">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-filament-widgets::widget>

<style>
    .rtl {
        direction: rtl;
    }
</style>