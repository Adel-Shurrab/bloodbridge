<x-filament-widgets::widget>
    @php($data = $this->getEligibilityData())

    <div class="timer-banner">
        @if(!$data['eligible_now'])

        <p class="timer-label">
            أهلية التبرع التالية في :
        </p>
        @endif
        <div class="timer-display">

            <i class="fa-regular fa-hourglass-half"></i>

            {{-- إذا مؤهل الآن --}}
            @if($data['eligible_now'])
                <h2>{{ $data['message'] }}</h2>
            @elseif($data['timestamp'])
                {{-- العدّ التنازلي --}}
                <h2
                    x-data="{
                        target: {{ $data['timestamp'] }} * 1000,
                        now: Date.now(),
                        tick() { this.now = Date.now() },
                        get diff() { return Math.max(0, this.target - this.now) },
                        get d() { return Math.floor(this.diff / 86400000) },
                        get h() { return Math.floor((this.diff % 86400000) / 3600000) },
                        get m() { return Math.floor((this.diff % 3600000) / 60000) },
                        get s() { return Math.floor((this.diff % 60000) / 1000) }
                    }"
                    x-init="setInterval(() => tick(), 1000)"
                >
                    <span x-text="d + 'd'"></span>
                    <span x-text="h + 'h'"></span>
                    <span x-text="m + 'm'"></span>
                    <span x-text="s + 's'"></span>
                </h2>
            @else
                <h2>{{ $data['message'] }}</h2>
            @endif

        </div>

    </div>

    {{-- نفس CSS تبعك بدون تغيير --}}
    <style>
        .timer-banner {
            background-color: #fff;
            border: 1px solid #D32F2F;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
            background: linear-gradient(to right, #fff, #fffafa, #fff);
        }

        .timer-label {
            font-size: 12px;
            color: #888;
            margin-bottom: 5px;
        }

        .timer-display {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            color: #D32F2F;
        }

        .timer-display h2 {
            font-size: 24px;
            font-weight: 600;
        }
    </style>
</x-filament-widgets::widget>
