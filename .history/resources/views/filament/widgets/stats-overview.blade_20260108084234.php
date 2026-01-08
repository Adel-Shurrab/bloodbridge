<x-filament-widgets::widget>
    <div class="custom-stats-grid">
        @foreach($stats as $stat)
            <div class="stat-card" style="border-bottom: 4px solid {{ $stat['border'] }};">
                <div class="stat-top">
                    {{-- Icon Box --}}
                    <div class="stat-icon-box" style="background: {{ $stat['bg'] }};">
                        <x-filament::icon
                            :icon="$stat['icon']"
                            class="stat-icon"
                            style="color: {{ $stat['color'] }}; width: 2rem; height: 2rem;"
                        />
                    </div>
                    {{-- Label --}}
                    <span class="stat-label">{{ $stat['label'] }}</span>
                </div>
                <div class="stat-bottom">
                    {{-- Value --}}
                    <span class="stat-value" style="color: {{ $stat['color'] }};">
                        {{ $stat['value'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .custom-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            direction: rtl;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 2rem;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 180px;
            transition: transform 0.2s, box-shadow 0.2s, background-color 0.3s;
        }
        .dark .stat-card {
            background: #111827;
            border: 1px solid #1f2937;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .stat-label {
            font-size: 1.15rem;
            font-weight: 700;
            color: #9ca3af;
            text-align: right;
            max-width: 60%;
        }
        .dark .stat-label {
            color: #6b7280;
        }
        .stat-icon-box {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
            flex-shrink: 0;
        }
        .dark .stat-icon-box {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
        .stat-bottom {
            text-align: left;
            margin-top: 1rem;
        }
        .stat-value {
            font-size: 3.5rem;
            font-weight: 950;
            line-height: 1;
        }

        @media (max-width: 640px) {
            .custom-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-filament-widgets::widget>