<x-filament-panels::page>
    <div class="statistics-page-layout">
        <div class="top-bar">
            <div class="top-bar-title">
                <h1>{{ __('admin.statistics_title') }}</h1>
                <p class="top-bar-subtitle">
                    {{ __('admin.statistics_subtitle') }}
                </p>
            </div>
            <div class="top-bar-actions">
                <div class="profile-section">
                    <div class="profile-info">
                        <span class="profile-name">{{ auth()->user()->name }}</span>
                        <span class="profile-role">{{ __('admin.system_administrator') }}</span>
                    </div>
                    <img src="{{ filament()->getUserAvatarUrl(auth()->user()) }}" alt="{{ __('admin.profile') }}" class="profile-img">
                </div>
            </div>
        </div>

        <div class="stats-section mb-8">
            @livewire(\App\Filament\Admin\Widgets\AdvancedStatsOverview::class)
        </div>

        <div class="middle-grid">
            <div class="grid-col">
                @livewire(\App\Filament\Admin\Widgets\RecentActivityWidget::class)
            </div>
            <div class="grid-col">
                @livewire(\App\Filament\Admin\Widgets\BloodTypeDemandWidget::class)
            </div>
        </div>

        <div class="chart-section mt-8">
            @livewire(\App\Filament\Admin\Widgets\EngagementChartWidget::class)
        </div>

        <div class="mt-8">
            @livewire('app.filament.admin.widgets.m-l-scoring-monitor-widget')
        </div>
    </div>

    <style>
        .statistics-page-layout {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            border: 1px solid rgba(211, 47, 47, 0.05);
            border-radius: 24px;
            background: #ffffff;
            padding: 1.5rem 2.5rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.06);
        }

        .dark .top-bar {
            background: #111827;
            border-color: #1f2937;
        }

        .top-bar-title h1 {
            margin-bottom: 0.25rem;
            color: #1f2937;
            font-size: 2.25rem;
            font-weight: 800;
        }

        .dark .top-bar-title h1 {
            color: #f9fafb;
        }

        .top-bar-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            border-radius: 16px;
            background: #fff5f5;
            padding: 0.75rem 1.25rem;
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
            color: #1f2937;
            font-weight: 800;
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
            border: 3px solid #d32f2f;
            border-radius: 50%;
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

        .fi-wi-chart {
            border-radius: 24px !important;
            background: white !important;
            padding: 2rem !important;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05) !important;
        }

        .dark .fi-wi-chart {
            border: 1px solid #1f2937 !important;
            background: #111827 !important;
        }
    </style>
</x-filament-panels::page>
