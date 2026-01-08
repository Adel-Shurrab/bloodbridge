<x-filament-panels::page>
    <div class="statistics-page-layout">
        {{-- Custom Header for this page --}}
        <div class="top-bar">
            <div class="top-bar-title">
                <h1>الإحصائيات</h1>
                <p class="top-bar-subtitle">
                    المقاييس والرؤى الرئيسية لطلبات التبرع بالدم.
                </p>
            </div>
            <div class="top-bar-actions">
                {{-- Notification Button (Matching Image) --}}
                <div class="notification-container">
                    <button class="notification-btn">
                        <x-filament::icon icon="heroicon-s-bell" class="bell-icon-svg" />
                        <span class="notification-badge">2</span>
                    </button>
                </div>

                <div class="profile-section">
                    <div class="profile-info">
                        <span class="profile-name">{{ auth()->user()->name }}</span>
                        <span class="profile-role">مسؤول النظام</span>
                    </div>
                    <img src="{{ filament()->getUserAvatarUrl(auth()->user()) }}" alt="Profile" class="profile-img">
                </div>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="stats-section mb-8">
            @livewire(\App\Filament\Widgets\AdvancedStatsOverview::class)
        </div>

        {{-- Middle Section: Activity and Demand --}}
        <div class="middle-grid">
            <div class="grid-col">
                @livewire(\App\Filament\Widgets\RecentActivityWidget::class)
            </div>
            <div class="grid-col">
                @livewire(\App\Filament\Widgets\BloodTypeDemandWidget::class)
            </div>
        </div>

        {{-- Bottom Section: Chart --}}
        <div class="chart-section mt-8">
            @livewire(\App\Filament\Widgets\EngagementChartWidget::class)
        </div>
    </div>

    <style>
        .statistics-page-layout {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Reusing the Header Styles (scoped to statistics page) */
        .top-bar {
            background: #ffffff;
            padding: 1.5rem 2.5rem;
            border-radius: 24px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border: 1px solid rgba(211, 47, 47, 0.05);
            direction: rtl;
        }

        .dark .top-bar {
            background: #111827;
            border-color: #1f2937;
        }

        .top-bar-title h1 {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .dark .top-bar-title h1 {
            color: #f9fafb;
        }

        .top-bar-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .notification-container {
            margin-left: 1.5rem;
        }

        .notification-btn {
            background: #fff5f5;
            border: 1px solid #fee2e2;
            border-radius: 14px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
        }

        .dark .notification-btn {
            background: #1f1212;
            border-color: #450a0a;
        }

        .notification-btn:hover {
            transform: scale(1.05) rotate(-5deg);
        }

        .bell-icon-svg {
            width: 1.75rem;
            height: 1.75rem;
            color: #ef4444;
        }

        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: white;
            font-size: 0.75rem;
            font-weight: 800;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }

        .dark .notification-badge {
            border-color: #111827;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1.25rem;
            background: #fff5f5;
            border-radius: 16px;
        }

        .dark .profile-section {
            background: #1f1212;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .profile-name {
            font-weight: 800;
            color: #1f2937;
        }

        .dark .profile-name {
            color: #f3f4f6;
        }

        .profile-role {
            color: #6b7280;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid #d32f2f;
        }

        .middle-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        @media (max-width: 1024px) {
            .middle-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Customizing Filament Chart Container */
        .fi-wi-chart {
            background: white !important;
            border-radius: 24px !important;
            padding: 2rem !important;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05) !important;
        }

        .dark .fi-wi-chart {
            background: #111827 !important;
            border: 1px solid #1f2937 !important;
        }
    </style>
</x-filament-panels::page>