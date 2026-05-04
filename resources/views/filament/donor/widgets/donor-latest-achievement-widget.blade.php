<x-filament-widgets::widget>
    @php($data = $this->getLatestAchievementData())

    <div class="ach-summary-card">

        {{-- Left: Level + Points --}}
        <div class="ach-summary-left">
            <span class="ach-level-pill">{{ __('donor.level') }} {{ $data['level'] }}</span>
            <div class="ach-points-row">
                <span class="ach-points-num">{{ number_format($data['points']) }}</span>
                <span class="ach-points-unit">{{ __('donor.points') }}</span>
            </div>
        </div>

        <div class="ach-divider"></div>

        {{-- Right: Latest badge or call-to-action --}}
        <div class="ach-summary-right">
            @if($data['has_achievement'])
                <div class="ach-badge-row">
                    @php($ach = $data['latest']->achievement)
                    <div class="ach-icon-wrap ach-tier-{{ $ach->badge_type }}">
                        @if($ach->badge_icon)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($ach->badge_icon) }}"
                                alt="{{ $ach->getTranslation('name', app()->getLocale()) }}"
                                class="ach-badge-img"
                            />
                        @else
                            <x-filament::icon icon="heroicon-o-trophy" class="ach-badge-icon-fallback" />
                        @endif
                    </div>
                    <div class="ach-badge-info">
                        <span class="ach-badge-label">{{ __('donor.latest_achievement') }}</span>
                        <span class="ach-badge-name">{{ $ach->getTranslation('name', app()->getLocale()) }}</span>
                        <span class="ach-badge-date">{{ $data['latest']->earned_at?->toDateString() }}</span>
                    </div>
                </div>
                <a href="{{ \App\Filament\Donor\Pages\Achievements::getUrl() }}"
                   class="ach-view-link">
                    {{ __('donor.view_all_achievements') }} ({{ $data['total'] }})
                </a>
            @else
                <div class="ach-empty-row">
                    <x-filament::icon icon="heroicon-o-trophy" class="ach-empty-icon" />
                    <div>
                        <span class="ach-empty-text">{{ __('donor.no_achievements_yet') }}</span>
                        <a href="{{ \App\Filament\Donor\Pages\Achievements::getUrl() }}"
                           class="ach-view-link">{{ __('donor.view_achievements') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .ach-summary-card {
            display: flex; align-items: center; gap: 1.5rem;
            background: #ffffff; border: 1px solid #fee2e2;
            border-radius: 1rem; padding: 1.25rem 1.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .dark .ach-summary-card {
            background: #111827; border-color: #450a0a;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .ach-summary-left { display: flex; flex-direction: column; align-items: center; gap: 0.4rem; flex-shrink: 0; }
        .ach-level-pill {
            background: #fee2e2; color: #b91c1c; font-weight: 800;
            font-size: 0.82rem; padding: 0.25rem 0.85rem;
            border-radius: 9999px; border: 1px solid #fecaca; white-space: nowrap;
        }
        .dark .ach-level-pill { background: #450a0a; color: #fca5a5; border-color: #7f1d1d; }
        .ach-points-row { display: flex; align-items: baseline; gap: 0.25rem; }
        .ach-points-num { font-size: 1.75rem; font-weight: 800; color: #dc2626; line-height: 1; }
        .dark .ach-points-num { color: #ef4444; }
        .ach-points-unit { font-size: 0.72rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; }
        .ach-divider { width: 1px; background: #f3f4f6; align-self: stretch; }
        .dark .ach-divider { background: #1f2937; }
        .ach-summary-right { flex: 1; display: flex; flex-direction: column; gap: 0.5rem; }
        .ach-badge-row { display: flex; align-items: center; gap: 0.85rem; }
        .ach-icon-wrap {
            width: 3.5rem; height: 3.5rem; border-radius: 0.75rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden;
            border: 2px solid rgba(0,0,0,0.06);
        }
        /* Badge tier backgrounds */
        .ach-tier-bronze   { background: #fef3c7; }
        .ach-tier-silver   { background: #f3f4f6; }
        .ach-tier-gold     { background: #fef9c3; }
        .ach-tier-platinum { background: #e0f2fe; }
        .ach-tier-diamond  { background: #ede9fe; }
        .dark .ach-tier-bronze   { background: #78350f; }
        .dark .ach-tier-silver   { background: #374151; }
        .dark .ach-tier-gold     { background: #713f12; }
        .dark .ach-tier-platinum { background: #0c4a6e; }
        .dark .ach-tier-diamond  { background: #4c1d95; }
        .ach-badge-img { width: 100%; height: 100%; object-fit: contain; padding: 0.35rem; }
        .ach-badge-icon-fallback { width: 1.5rem; height: 1.5rem; color: #9ca3af; }
        .ach-badge-info { display: flex; flex-direction: column; gap: 0.1rem; }
        .ach-badge-label { font-size: 0.7rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .ach-badge-name { font-weight: 700; color: #1f2937; font-size: 1rem; }
        .dark .ach-badge-name { color: #f3f4f6; }
        .ach-badge-date { font-size: 0.75rem; color: #6b7280; }
        .ach-view-link { font-size: 0.8rem; color: #dc2626; font-weight: 600; text-decoration: none; width: fit-content; }
        .ach-view-link:hover { text-decoration: underline; }
        .ach-empty-row { display: flex; align-items: center; gap: 0.75rem; }
        .ach-empty-icon { width: 2rem; height: 2rem; color: #d1d5db; }
        .dark .ach-empty-icon { color: #374151; }
        .ach-empty-text { display: block; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.2rem; }
        @media (max-width: 640px) {
            .ach-summary-card { flex-direction: column; text-align: center; }
            .ach-divider { width: 100%; height: 1px; }
            .ach-badge-row { flex-direction: column; }
        }
    </style>
</x-filament-widgets::widget>
