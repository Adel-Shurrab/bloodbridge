<x-filament-panels::page>
    @php($data = $this->getAchievementsData())

    <div class="ach-page">

        {{-- ── Header ── --}}
        <div class="ach-header-card">
            <div class="ach-header-left">
                <span class="ach-level-pill">{{ __('donor.level') }} {{ $data['level'] }}</span>
                <div class="ach-pts-display">
                    <span class="ach-pts-num">{{ number_format($data['points']) }}</span>
                    <span class="ach-pts-unit">{{ __('donor.points') }}</span>
                </div>
            </div>
            <p class="ach-header-summary">
                {{ __('donor.achievements_earned_count', ['count' => count($data['earned'])]) }}
                &middot;
                {{ __('donor.achievements_remaining_count', ['count' => count($data['locked'])]) }}
                {{ __('donor.achievements_remaining_label') }}
            </p>
        </div>

        {{-- ── Earned ── --}}
        @if(count($data['earned']) > 0)
        <div class="ach-section">
            <h2 class="ach-section-title">
                <x-filament::icon icon="heroicon-o-check-badge" class="ach-section-icon" />
                {{ __('donor.earned_achievements') }}
            </h2>
            <div class="ach-grid">
                @foreach($data['earned'] as $row)
                @php($ach = $row->achievement)
                <div class="ach-card ach-card--earned ach-tier-{{ $ach->badge_type }}">
                    <div class="ach-card-icon-wrap">
                        @if($ach->badge_icon)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($ach->badge_icon) }}"
                                alt="{{ $ach->getTranslation('name', app()->getLocale()) }}"
                                class="ach-card-badge-img"
                            />
                        @else
                            <x-filament::icon icon="heroicon-o-trophy" class="ach-card-icon" />
                        @endif
                    </div>
                    <div class="ach-card-body">
                        <span class="ach-card-name">
                            {{ $ach->getTranslation('name', app()->getLocale()) }}
                        </span>
                        <span class="ach-card-desc">
                            {{ $ach->getTranslation('description', app()->getLocale()) }}
                        </span>
                        <span class="ach-card-earned-at">
                            {{ __('donor.earned_on') }} {{ $row->earned_at?->toDateString() }}
                        </span>
                    </div>
                    <span class="ach-tier-label">{{ $ach->badge_type }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Locked ── --}}
        @if(count($data['locked']) > 0)
        <div class="ach-section">
            <h2 class="ach-section-title">
                <x-filament::icon icon="heroicon-o-lock-closed" class="ach-section-icon" />
                {{ __('donor.locked_achievements') }}
            </h2>
            <div class="ach-grid">
                @foreach($data['locked'] as $item)
                @php($ach = $item['achievement'])
                <div class="ach-card ach-card--locked">
                    <div class="ach-card-icon-wrap ach-locked-wrap">
                        @if($ach->badge_icon)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($ach->badge_icon) }}"
                                alt="{{ $ach->getTranslation('name', app()->getLocale()) }}"
                                class="ach-card-badge-img ach-card-badge-img--locked"
                            />
                        @else
                            <x-filament::icon icon="heroicon-o-trophy" class="ach-card-icon ach-card-icon--locked" />
                        @endif
                    </div>
                    <div class="ach-card-body">
                        <span class="ach-card-name ach-card-name--locked">
                            {{ $ach->getTranslation('name', app()->getLocale()) }}
                        </span>
                        {{-- "How to earn it" — pulled from achievement description --}}
                        <span class="ach-card-desc">
                            {{ $ach->getTranslation('description', app()->getLocale()) }}
                        </span>
                        {{-- Progress bar (only shown for simple donation/points criteria) --}}
                        @if(in_array($ach->criteria_type, ['donations', 'points']))
                        <div class="ach-progress-wrap">
                            <div class="ach-progress-bar">
                                <div class="ach-progress-fill" style="width: {{ $item['progress'] }}%"></div>
                            </div>
                            <span class="ach-progress-label">
                                {{ $item['current'] }} / {{ $item['target'] }}
                                ({{ $item['progress'] }}%)
                            </span>
                        </div>
                        @endif
                    </div>
                    <span class="ach-tier-label">{{ $ach->badge_type }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Empty state ── --}}
        @if(count($data['earned']) === 0 && count($data['locked']) === 0)
        <div class="ach-empty">
            <x-filament::icon icon="heroicon-o-trophy" class="ach-empty-icon" />
            <p>{{ __('donor.no_achievements_defined') }}</p>
        </div>
        @endif
    </div>

    <style>
        .ach-page { display: flex; flex-direction: column; gap: 2rem; }

        /* Header */
        .ach-header-card {
            display: flex; align-items: center; justify-content: space-between; gap: 1.5rem;
            background: #ffffff; border: 1px solid #fee2e2;
            border-radius: 1rem; padding: 1.25rem 1.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .dark .ach-header-card { background: #111827; border-color: #450a0a; }
        .ach-header-left { display: flex; align-items: center; gap: 1rem; }
        .ach-level-pill {
            background: #fee2e2; color: #b91c1c; font-weight: 800;
            font-size: 0.82rem; padding: 0.3rem 1rem;
            border-radius: 9999px; border: 1px solid #fecaca;
        }
        .dark .ach-level-pill { background: #450a0a; color: #fca5a5; border-color: #7f1d1d; }
        .ach-pts-display { display: flex; align-items: baseline; gap: 0.3rem; }
        .ach-pts-num { font-size: 2rem; font-weight: 800; color: #dc2626; line-height: 1; }
        .dark .ach-pts-num { color: #ef4444; }
        .ach-pts-unit { font-size: 0.78rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; }
        .ach-header-summary { color: #6b7280; font-size: 0.88rem; margin: 0; }
        .dark .ach-header-summary { color: #9ca3af; }

        /* Section */
        .ach-section { display: flex; flex-direction: column; gap: 1rem; }
        .ach-section-title {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 1.1rem; font-weight: 700; color: #1f2937; margin: 0;
        }
        .dark .ach-section-title { color: #f3f4f6; }
        .ach-section-icon { width: 1.2rem; height: 1.2rem; color: #dc2626; }

        /* Grid */
        .ach-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }

        /* Card */
        .ach-card {
            display: flex; flex-direction: column; gap: 0.75rem;
            background: #ffffff; border-radius: 1rem;
            padding: 1.25rem; position: relative; overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f3f4f6;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .ach-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .dark .ach-card { background: #1f2937; border-color: #374151; }

        /* Earned card coloured left border based on tier */
        .ach-card--earned { border-left: 4px solid #9ca3af; }
        .ach-card--earned.ach-tier-bronze   { border-left-color: #d97706; }
        .ach-card--earned.ach-tier-silver   { border-left-color: #9ca3af; }
        .ach-card--earned.ach-tier-gold     { border-left-color: #eab308; }
        .ach-card--earned.ach-tier-platinum { border-left-color: #38bdf8; }
        .ach-card--earned.ach-tier-diamond  { border-left-color: #a78bfa; }

        /* Locked card muted */
        .ach-card--locked { opacity: 0.82; }

        /* Icon wrap */
        .ach-card-icon-wrap {
            width: 3rem; height: 3rem; border-radius: 0.65rem;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; background: #fee2e2;
        }
        .dark .ach-card-icon-wrap { background: #450a0a; }
        .ach-locked-wrap { background: #f3f4f6; }
        .dark .ach-locked-wrap { background: #374151; }
        .ach-card-badge-img { width: 100%; height: 100%; object-fit: contain; padding: 0.3rem; }
        .ach-card-badge-img--locked { opacity: 0.5; filter: grayscale(100%); }
        .ach-card-icon { width: 1.4rem; height: 1.4rem; color: #dc2626; }
        .ach-card-icon--locked { color: #9ca3af; }

        /* Card body */
        .ach-card-body { display: flex; flex-direction: column; gap: 0.2rem; flex: 1; }
        .ach-card-name { font-weight: 700; color: #1f2937; font-size: 0.95rem; }
        .dark .ach-card-name { color: #f3f4f6; }
        .ach-card-name--locked { color: #6b7280; }
        .dark .ach-card-name--locked { color: #9ca3af; }
        .ach-card-desc { font-size: 0.78rem; color: #6b7280; line-height: 1.4; }
        .dark .ach-card-desc { color: #9ca3af; }
        .ach-card-earned-at { font-size: 0.72rem; color: #9ca3af; margin-top: 0.2rem; }

        /* Progress */
        .ach-progress-wrap { display: flex; flex-direction: column; gap: 0.2rem; margin-top: 0.3rem; }
        .ach-progress-bar { background: #f3f4f6; border-radius: 9999px; height: 5px; overflow: hidden; }
        .dark .ach-progress-bar { background: #374151; }
        .ach-progress-fill {
            background: linear-gradient(to right, #ef4444, #dc2626);
            height: 100%; border-radius: 9999px; transition: width 0.4s;
        }
        .ach-progress-label { font-size: 0.7rem; color: #9ca3af; }

        /* Tier label corner decoration */
        .ach-tier-label {
            position: absolute; top: 0.5rem; right: 0.6rem;
            font-size: 0.62rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.06em; color: #9ca3af; opacity: 0.7;
        }

        /* Empty */
        .ach-empty {
            display: flex; flex-direction: column; align-items: center; gap: 0.75rem;
            padding: 3rem; text-align: center;
            background: #ffffff; border-radius: 1rem; border: 1px dashed #e5e7eb;
        }
        .dark .ach-empty { background: #1f2937; border-color: #374151; }
        .ach-empty-icon { width: 3rem; height: 3rem; color: #e5e7eb; }
        .dark .ach-empty-icon { color: #374151; }
        .ach-empty p { color: #9ca3af; font-size: 0.95rem; margin: 0; }

        /* Responsive */
        @media (max-width: 640px) {
            .ach-header-card { flex-direction: column; text-align: center; }
            .ach-grid { grid-template-columns: 1fr; }
        }
    </style>
</x-filament-panels::page>
