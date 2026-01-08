<x-filament-widgets::widget>
    <div class="blood-demand-card">
        <h2 class="card-title">فصيلة الدم الأكثر احتياجاً</h2>

        @php $data = $this->getDemandData(); @endphp

        <div class="most-needed-section">
            <span class="blood-type-big">{{ $data['most_needed'] }}</span>
            <span class="demand-label">الأكثر طلباً</span>
        </div>

        <div class="demand-grid">
            @foreach($data['breakdown'] as $item)
                <div class="demand-item">
                    <span class="item-value">{{ $item['value'] }}</span>
                    <span class="item-label">{{ $item['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .blood-demand-card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.05);
            height: 100%;
            text-align: center;
            direction: rtl;
        }

        .dark .blood-demand-card {
            background: #111827;
            border: 1px solid #1f2937;
        }

        .most-needed-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 3rem 0;
        }

        .blood-type-big {
            font-size: 6rem;
            font-weight: 900;
            color: #ef4444;
            line-height: 1;
        }

        .demand-label {
            font-size: 1.25rem;
            color: #9ca3af;
            font-weight: 700;
            margin-top: 1rem;
        }

        .demand-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-top: auto;
        }

        .demand-item {
            background: #f9fafb;
            border-radius: 16px;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dark .demand-item {
            background: rgba(255, 255, 255, 0.02);
        }

        .item-label {
            font-weight: 800;
            color: #1f2937;
            font-size: 1.1rem;
        }

        .dark .item-label {
            color: #f3f4f6;
        }

        .item-value {
            color: #ef4444;
            font-weight: 800;
            font-size: 1.1rem;
        }
    </style>
</x-filament-widgets::widget>