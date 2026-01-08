<x-filament-widgets::widget>
    <div class="fi-section rounded-3xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
        <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-10 gap-8" style="direction: rtl;">
            
            {{-- Right side: Dashboard Title and Welcome Message --}}
            <div class="flex flex-col text-right order-1 md:order-2">
                <h1 class="text-4xl font-black text-gray-800 dark:text-white mb-2 leading-none">
                    لوحة التحكم
                </h1>
                <p class="text-gray-500 dark:text-gray-400 text-xl font-medium">
                    مرحباً بعودتك، المشرف. إليك ما يحدث مع <span class="text-red-500 font-bold">BloodBridge</span>.
                </p>
            </div>

            {{-- Left side: Profile and Notifications --}}
            <div class="flex items-center gap-6 order-2 md:order-1">
                {{-- Profile Section --}}
                <div class="flex items-center gap-4 bg-gray-50 dark:bg-white/5 px-6 py-4 rounded-3xl ring-1 ring-gray-950/5 dark:ring-white/10">
                    <div class="flex flex-col text-right">
                        <span class="text-2xl font-bold text-gray-800 dark:text-gray-100 leading-tight">
                            {{ auth()->user()->name }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">
                            {{ auth()->user()->role === \App\Models\User::ROLE_ADMIN ? 'مسؤول النظام' : 'مؤسسة' }}
                        </span>
                    </div>
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full border-2 border-white dark:border-gray-800 shadow-md ring-4 ring-gray-50 dark:ring-white/5 overflow-hidden">
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
                        class="w-16 h-16 flex items-center justify-center bg-gray-50 dark:bg-white/5 rounded-3xl ring-1 ring-gray-950/5 dark:ring-white/10 transition-all hover:bg-gray-100 dark:hover:bg-white/10 active:scale-95 cursor-pointer focus:outline-none"
                    >
                        <x-filament::icon
                            icon="heroicon-s-bell"
                            class="w-8 h-8 text-gray-400"
                            style="width: 2rem; height: 2rem;"
                        />
                        
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-[11px] font-bold text-white border-2 border-white dark:border-gray-900 shadow-sm">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-filament-widgets::widget>