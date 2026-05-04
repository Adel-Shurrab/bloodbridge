<x-filament-panels::page>

    {{-- ── Control Panel ─────────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 shadow-sm">
        <div class="mb-4">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                {{ __('filament.pages.donor-scoring-simulation.select_request') }}
            </h2>
            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                {{ __('filament.pages.donor-scoring-simulation.description') }}
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('filament.pages.donor-scoring-simulation.blood_request') }}
                </label>
                <div class="relative">
                    <select
                        wire:model="bloodRequestId"
                        class="block w-full appearance-none rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2.5 ps-3 pe-10 text-sm text-gray-900 dark:text-gray-100 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                    >
                        <option value="">{{ __('filament.pages.donor-scoring-simulation.choose_request') }}</option>
                        @foreach($this->getBloodRequestOptions() as $id => $label)
                            <option value="{{ $id }}" @selected($bloodRequestId == $id)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 end-3 flex items-center">
                        <x-heroicon-m-chevron-down class="h-4 w-4 text-gray-400" />
                    </div>
                </div>
            </div>

            <button
                wire:click="runSimulation"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed"
                class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-60 dark:focus:ring-offset-gray-900"
            >
                <x-heroicon-m-play class="h-4 w-4" wire:loading.remove wire:target="runSimulation" />
                <svg wire:loading wire:target="runSimulation" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span wire:loading.remove wire:target="runSimulation">{{ __('filament.pages.donor-scoring-simulation.run_simulation') }}</span>
                <span wire:loading wire:target="runSimulation">{{ __('filament.pages.donor-scoring-simulation.running') }}…</span>
            </button>
        </div>
    </div>

    @if($hasRun)

        {{-- ── Summary Cards ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-5 gap-3">
            @php
                $cards = [
                    ['value' => $summaryCards['total_eligible'],    'label' => __('filament.pages.donor-scoring-simulation.total_eligible'),    'color' => 'gray'],
                    ['value' => $summaryCards['exploitation_pool'], 'label' => __('filament.pages.donor-scoring-simulation.exploitation_pool'), 'color' => 'blue'],
                    ['value' => $summaryCards['exploration_pool'],  'label' => __('filament.pages.donor-scoring-simulation.exploration_pool'),  'color' => 'yellow'],
                    ['value' => $summaryCards['selected'] . ' / ' . $summaryCards['budget'], 'label' => __('filament.pages.donor-scoring-simulation.notified_budget'), 'color' => 'red'],
                    ['value' => $summaryCards['cold_start'],        'label' => __('filament.pages.donor-scoring-simulation.cold_start'),        'color' => 'purple'],
                ];
                $colorMap = [
                    'gray'   => ['border' => 'border-gray-200 dark:border-gray-700',     'bg' => 'bg-white dark:bg-gray-900',          'val' => 'text-gray-900 dark:text-white',        'lbl' => 'text-gray-500 dark:text-gray-400'],
                    'blue'   => ['border' => 'border-blue-200 dark:border-blue-800',     'bg' => 'bg-blue-50 dark:bg-blue-900/20',     'val' => 'text-blue-700 dark:text-blue-300',     'lbl' => 'text-blue-500 dark:text-blue-400'],
                    'yellow' => ['border' => 'border-yellow-200 dark:border-yellow-700', 'bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'val' => 'text-yellow-700 dark:text-yellow-300', 'lbl' => 'text-yellow-600 dark:text-yellow-400'],
                    'red'    => ['border' => 'border-red-200 dark:border-red-800',       'bg' => 'bg-red-50 dark:bg-red-900/20',       'val' => 'text-red-700 dark:text-red-300',       'lbl' => 'text-red-500 dark:text-red-400'],
                    'purple' => ['border' => 'border-purple-200 dark:border-purple-800', 'bg' => 'bg-purple-50 dark:bg-purple-900/20', 'val' => 'text-purple-700 dark:text-purple-300', 'lbl' => 'text-purple-500 dark:text-purple-400'],
                ];
            @endphp

            @foreach($cards as $card)
            @php $c = $colorMap[$card['color']]; @endphp
            <div class="rounded-2xl border {{ $c['border'] }} {{ $c['bg'] }} p-4 shadow-sm">
                <p class="text-2xl font-bold {{ $c['val'] }}">{{ $card['value'] }}</p>
                <p class="mt-1 text-xs {{ $c['lbl'] }}">{{ $card['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Source Breakdown --}}
        @if(!empty($summaryCards['source_breakdown']))
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                {{ __('filament.pages.donor-scoring-simulation.source_breakdown') }}:
            </span>
            @foreach($summaryCards['source_breakdown'] as $source => $count)
                @php
                    $badgeClass = match($source) {
                        'db_cache'   => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 ring-1 ring-green-200 dark:ring-green-700',
                        'fastapi'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 ring-1 ring-blue-200 dark:ring-blue-700',
                        'rule_based' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300 ring-1 ring-orange-200 dark:ring-orange-700',
                        default      => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 ring-1 ring-purple-200 dark:ring-purple-700',
                    };
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClass }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                    {{ $source }}: {{ $count }}
                </span>
            @endforeach
        </div>
        @endif

        {{-- ── Donor Table ─────────────────────────────────────────────────── --}}
        @if(count($donorRows) > 0)
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-5 py-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ __('filament.pages.donor-scoring-simulation.eligible_donors') }}
                    </h3>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Sorted by Score (desc)</p>
                </div>
                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-3 py-1 text-xs font-semibold text-gray-700 dark:text-gray-300">
                    {{ count($donorRows) }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" dir="ltr">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/60 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="py-3 px-4 text-left font-semibold w-8">#</th>
                            <th class="py-3 px-4 text-left font-semibold w-48">{{ __('filament.pages.donor-scoring-simulation.col_donor') }}</th>
                            <th class="py-3 px-4 text-left font-semibold w-24">{{ __('filament.pages.donor-scoring-simulation.col_blood_type') }}</th>
                            <th class="py-3 px-4 text-left font-semibold w-24">{{ __('filament.pages.donor-scoring-simulation.col_distance') }}</th>
                            <th class="py-3 px-4 text-left font-semibold w-48">{{ __('filament.pages.donor-scoring-simulation.col_score') }}</th>
                            <th class="py-3 px-4 text-left font-semibold w-28">{{ __('filament.pages.donor-scoring-simulation.col_source') }}</th>
                            <th class="py-3 px-4 text-left font-semibold w-32">{{ __('filament.pages.donor-scoring-simulation.col_bucket') }}</th>
                            <th class="py-3 px-4 text-left font-semibold w-28">{{ __('filament.pages.donor-scoring-simulation.col_notify') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($donorRows as $i => $row)
                        @php $isExpanded = $expandedDonorId === $row['id']; @endphp

                        <tr
                            wire:click="toggleRow({{ $row['id'] }})"
                            class="cursor-pointer transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/40 {{ $row['notify'] ? 'bg-green-50/40 dark:bg-green-900/5' : '' }} {{ $isExpanded ? 'bg-blue-50/30 dark:bg-blue-900/10' : '' }}"
                        >

                            <td class="py-3 px-4 text-xs font-mono text-gray-400 dark:text-gray-600">
                                <span class="inline-block transition-transform {{ $isExpanded ? 'rotate-90' : '' }}">▶</span>
                            </td>

                            <td class="py-3 px-4 font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</td>

                            <td class="py-3 px-4">
                                <span class="inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/40 px-2.5 py-0.5 text-xs font-bold text-red-700 dark:text-red-300 ring-1 ring-red-200 dark:ring-red-800">
                                    {{ $row['blood_type'] }}
                                </span>
                            </td>

                            <td class="py-3 px-4 tabular-nums text-xs">
                                @if($row['distance'] !== '—')
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $row['distance'] }}</span>
                                    <span class="text-gray-400"> كم</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="py-3 px-4">
                                @php
                                    $pct       = (int) round($row['score'] * 100);
                                    $barColor  = $row['score'] >= 0.7 ? 'bg-green-500' : ($row['score'] >= 0.4 ? 'bg-yellow-400' : 'bg-red-400');
                                    $textColor = $row['score'] >= 0.7 ? 'text-green-600 dark:text-green-400' : ($row['score'] >= 0.4 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-500 dark:text-red-400');
                                @endphp
                                <div class="flex items-center gap-2.5">
                                    <div class="relative h-1.5 w-24 rounded-full bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                                        <div class="absolute inset-y-0 left-0 rounded-full {{ $barColor }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="w-9 text-left text-xs font-semibold font-mono {{ $textColor }}">{{ $pct }}%</span>
                                </div>
                            </td>

                            <td class="py-3 px-4">
                                @php
                                    [$srcLabel, $srcClass] = match($row['source']) {
                                        'db_cache'   => ['db_cache',    'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
                                        'fastapi'    => ['fastapi',     'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'],
                                        'rule_based' => ['rule_based',  'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'],
                                        default      => [$row['source'], 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300'],
                                    };
                                @endphp
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $srcClass }}">{{ $srcLabel }}</span>
                            </td>

                            <td class="py-3 px-4">
                                @if($row['bucket'] === 'exploitation')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 dark:text-blue-400">
                                        <span class="h-2 w-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                                        {{ __('filament.pages.donor-scoring-simulation.exploitation') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-yellow-600 dark:text-yellow-400">
                                        <span class="h-2 w-2 rounded-full bg-yellow-400 flex-shrink-0"></span>
                                        {{ __('filament.pages.donor-scoring-simulation.exploration') }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-center">
                                @if($row['notify'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-0.5 text-xs font-semibold text-green-700 dark:text-green-400">
                                        ✓ {{ __('filament.pages.donor-scoring-simulation.will_notify') }}
                                    </span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                @endif
                            </td>
                        </tr>

                        {{-- ── Expanded Details Row ── --}}
                        @if($isExpanded)
                        <tr class="bg-blue-50/40 dark:bg-blue-900/10 border-b border-blue-100 dark:border-blue-900/30">
                            <td></td>
                            <td colspan="7" class="px-6 py-4">
                                @php
                                    $total    = $row['total_responses'];
                                    $accepted = $row['accepted_count'];
                                    $noShow   = $row['no_show_count'];
                                    $donations= $row['total_donations'];
                                    $lastDate = $row['last_responded_at']
                                        ? \Carbon\Carbon::parse($row['last_responded_at'])->diffForHumans()
                                        : null;
                                    $acceptRate = $total > 0
                                        ? round(($accepted / ($total + $noShow)) * 100)
                                        : null;
                                @endphp

                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4" dir="ltr">

                                    {{-- Total Responses --}}
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Total Responses</p>
                                        <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $total }}</p>
                                        <div class="mt-1 flex gap-2 text-xs">
                                            <span class="text-green-600 dark:text-green-400">✓ {{ $accepted }} accepted</span>
                                            <span class="text-red-500">✗ {{ $noShow }} no-show</span>
                                        </div>
                                    </div>

                                    {{-- Acceptance Rate --}}
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Acceptance Rate</p>
                                        @if($acceptRate !== null)
                                            @php
                                                $rateColor = $acceptRate >= 70 ? 'text-green-600 dark:text-green-400' : ($acceptRate >= 40 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-500');
                                            @endphp
                                            <p class="text-xl font-bold {{ $rateColor }}">{{ $acceptRate }}%</p>
                                            <p class="mt-1 text-xs text-gray-400">{{ $accepted }} / {{ $total + $noShow }} adjusted</p>
                                        @else
                                            <p class="text-xl font-bold text-gray-400">—</p>
                                            <p class="mt-1 text-xs text-gray-400">No history</p>
                                        @endif
                                    </div>

                                    {{-- Total Donations --}}
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Total Donations</p>
                                        <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $donations }}</p>
                                        <p class="mt-1 text-xs text-gray-400">Loyalty: {{ min(round($donations / 10 * 100), 100) }}%</p>
                                    </div>

                                    {{-- Last Active --}}
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Last Active</p>
                                        @if($lastDate)
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $lastDate }}</p>
                                        @else
                                            <p class="text-sm font-semibold text-gray-400">Never responded</p>
                                        @endif
                                        <p class="mt-1 text-xs text-gray-400">
                                            Score source: <span class="font-medium">{{ $row['source'] }}</span>
                                            @if($row['source'] === 'db_cache' && $row['model_version'])
                                                @php
                                                    $mv = $row['model_version'];
                                                    $mvColor = str_contains($mv, 'xgboost') || $mv === 'fastapi'
                                                        ? 'text-blue-500'
                                                        : 'text-orange-500';
                                                @endphp
                                                → <span class="font-semibold {{ $mvColor }}">{{ $mv }}</span>
                                            @endif
                                        </p>
                                    </div>

                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @else
        <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-14 text-center">
            <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
            <p class="mt-3 text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ __('filament.pages.donor-scoring-simulation.no_eligible_donors') }}
            </p>
        </div>
        @endif

        {{-- Disclaimer --}}
        <p class="text-center text-xs text-gray-400 dark:text-gray-500">
            ⚠️ {{ __('filament.pages.donor-scoring-simulation.disclaimer') }}
        </p>

    @endif

</x-filament-panels::page>
