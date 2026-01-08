<x-filament-widgets::widget>
    <div class="custom-stats-grid">
        @foreach($stats as $stat)
            <div class="stat-card" style="border-bottom: 4px solid {{ str_contains($stat['color'], 'red') ? '#fee2e2' : (str_contains($stat['color'], 'blue') ? '#dbeafe' : (str_contains($stat['color'], 'emerald') ? '#dcfce7' : '#fce7f3')) }};">
                <div class="stat-top">
                    <div class="stat-icon-box" style="background: {{ str_contains($stat['bg'], 'red') ? '#fff5f5' : (str_contains($stat['bg'], 'blue') ? '#eff6ff' : (str_contains($stat['bg'], 'emerald') ? '#ecfdf5' : '#fff1f2')) }};">
                        <x-filament::icon
                            :icon="$stat['icon']"
                            class="stat-icon"
                            style="color: {{ str_contains($stat['color'], 'red') ? '#ef4444' : (str_contains($stat['color'], 'blue') ? '#2563eb' : (str_contains($stat['color'], 'emerald') ? '#10b981' : '#db2777')) }}; width: 2rem; height: 2rem;"
                        />
                    </div>
                    <span class="stat-label">{{ $stat['label'] }}</span>
                </div>
                <div class="stat-bottom">
                    <span class="stat-value" style="color: {{ str_contains($stat['color'], 'red') ? '#ef4444' : (str_contains($stat['color'], 'blue') ? '#2563eb' : (str_contains($stat['color'], 'emerald') ? '#10b981' : '#db2777')) }};">
                        {{ $stat['value'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .custom-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
            height: 180px;
            transition: transform 0.2s, box-shadow 0.2s;
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
            font-size: 1.25rem;
            font-weight: 700;
            color: #9ca3af;
            text-align: right;
        }
        .stat-icon-box {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-bottom {
            text-align: left;
        }
        .stat-value {
            font-size: 3.5rem;
            font-weight: 950;
            line-height: 1;
        }

        @media (max-width: 1024px) {
            .custom-stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 640px) {
            .custom-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-filament-widgets::widget>