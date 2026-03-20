<x-filament-widgets::widget>
    <div class="stats-overview">
        @foreach($stats as $stat)
            <div class="stat-card" style="border-color: {{ $stat['border'] }};">
                <div class="stat-card-header">
                    <div class="stat-card-icon-box" style="background: {{ $stat['bg_gradient'] ?? $stat['bg'] }};">
                        <x-filament::icon
                            :icon="$stat['icon']"
                            class="stat-card-icon-svg"
                            style="color: {{ $stat['color'] }};"
                        />
                    </div>
                    <span class="stat-card-title">{{ $stat['label'] }}</span>
                </div>
                <div class="stat-card-body">
                    <span class="stat-card-value" style="color: {{ $stat['color'] }};">
                        {{ $stat['value'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            
        }
        .stat-card {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 22px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 140px;
        }
        .dark .stat-card {
            background: #111827;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3);
        }
        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        }
        .stat-card-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .stat-card-icon-box {
            width: 45px;
            height: 45px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s;
        }
        .dark .stat-card-icon-box {
            background: rgba(255, 255, 255, 0.05) !important;
        }
        .stat-card:hover .stat-card-icon-box {
            transform: scale(1.1) rotate(-5deg);
        }
        .stat-card-icon-svg {
            width: 1.75rem;
            height: 1.75rem;
        }
        .stat-card-title {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 700;
        }
        .dark .stat-card-title {
            color: #9ca3af;
        }
        .stat-card-body {
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .stat-card-value {
            font-size: 2.25rem;
            font-weight: 900;
            line-height: 1;
        }

        @media (max-width: 640px) {
            .stats-overview {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-filament-widgets::widget>