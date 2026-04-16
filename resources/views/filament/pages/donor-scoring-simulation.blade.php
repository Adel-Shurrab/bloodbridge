<x-filament-panels::page>

    {{-- ── Control Panel ─────────────────────────────────────────────────── --}}
    <x-filament::section>
        <x-slot name="heading">{{ __('filament.pages.donor-scoring-simulation.select_request') }}</x-slot>
        <x-slot name="description">{{ __('filament.pages.donor-scoring-simulation.description') }}</x-slot>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('filament.pages.donor-scoring-simulation.blood_request') }}
                </label>
                <select wire:model="bloodRequestId"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                    <option value="">{{ __('filament.pages.donor-scoring-simulation.choose_request') }}</option>
                    @foreach($this->getBloodRequestOptions() as $id => $label)
                        <option value="{{ $id }}" @selected($bloodRequestId == $id)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <x-filament::button
                wire:click="runSimulation"
                wire:loading.attr="disabled"
                color="danger"
                icon="heroicon-o-play"
            >
                <span wire:loading.remove wire:target="runSimulation">
                    {{ __('filament.pages.donor-scoring-simulation.run_simulation') }}
                </span>
                <span wire:loading wire:target="runSimulation">
                    {{ __('filament.pages.donor-scoring-simulation.running') }}…
                </span>
            </x-filament::button>
        </div>
    </x-filament::section>

    @if($hasRun)

        {{-- ── Summary Cards ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">

            {{-- Total Eligible --}}
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $summaryCards['total_eligible'] }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('filament.pages.donor-scoring-simulation.total_eligible') }}</div>
            </div>

            {{-- Exploitation Pool --}}
            <div class="rounded-2xl border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/30 p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-blue-700 dark:text-blue-300">{{ $summaryCards['exploitation_pool'] }}</div>
                <div class="mt-1 text-xs text-blue-500 dark:text-blue-400">🔵 {{ __('filament.pages.donor-scoring-simulation.exploitation_pool') }}</div>
            </div>

            {{-- Exploration Pool --}}
            <div class="rounded-2xl border border-yellow-200 dark:border-yellow-700 bg-yellow-50 dark:bg-yellow-900/30 p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-yellow-700 dark:text-yellow-300">{{ $summaryCards['exploration_pool'] }}</div>
                <div class="mt-1 text-xs text-yellow-500 dark:text-yellow-400">🟡 {{ __('filament.pages.donor-scoring-simulation.exploration_pool') }}</div>
            </div>

            {{-- Selected / Budget --}}
            <div class="rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/30 p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-red-700 dark:text-red-300">
                    {{ $summaryCards['selected'] }}<span class="text-lg text-red-400">/{{ $summaryCards['budget'] }}</span>
                </div>
                <div class="mt-1 text-xs text-red-400 dark:text-red-400">{{ __('filament.pages.donor-scoring-simulation.notified_budget') }}</div>
            </div>

            {{-- Cold Start --}}
            <div class="rounded-2xl border border-purple-200 dark:border-purple-800 bg-purple-50 dark:bg-purple-900/30 p-4 text-center shadow-sm">
                <div class="text-3xl font-bold text-purple-700 dark:text-purple-300">{{ $summaryCards['cold_start'] }}</div>
                <div class="mt-1 text-xs text-purple-500 dark:text-purple-400">{{ __('filament.pages.donor-scoring-simulation.cold_start') }}</div>
            </div>
        </div>

        {{-- Source Breakdown Badges --}}
        @if(!empty($summaryCards['source_breakdown']))
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('filament.pages.donor-scoring-simulation.source_breakdown') }}:</span>
            @foreach($summaryCards['source_breakdown'] as $source => $count)
                @php
                    $badgeClass = match($source) {
                        'db_cache'   => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                        'fastapi'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
                        'rule_based' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                        default      => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                    };
                @endphp
                <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $badgeClass }}">
                    {{ $source }}: {{ $count }}
                </span>
            @endforeach
        </div>
        @endif

        {{-- ── Donor Table ─────────────────────────────────────────────────── --}}
        @if(count($donorRows) > 0)
        <x-filament::section>
            <x-slot name="heading">
                {{ __('filament.pages.donor-scoring-simulation.eligible_donors') }}
                <span class="ms-2 inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-300">
                    {{ count($donorRows) }}
                </span>
            </x-slot>

            <div class="overflow-x-auto -mx-6 -mb-6">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <th class="py-3 px-4 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">#</th>
                            <th class="py-3 px-4 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('filament.pages.donor-scoring-simulation.col_donor') }}</th>
                            <th class="py-3 px-4 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('filament.pages.donor-scoring-simulation.col_blood_type') }}</th>
                            <th class="py-3 px-4 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('filament.pages.donor-scoring-simulation.col_distance') }}</th>
                            <th class="py-3 px-4 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 min-w-[160px]">{{ __('filament.pages.donor-scoring-simulation.col_score') }}</th>
                            <th class="py-3 px-4 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('filament.pages.donor-scoring-simulation.col_source') }}</th>
                            <th class="py-3 px-4 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('filament.pages.donor-scoring-simulation.col_bucket') }}</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('filament.pages.donor-scoring-simulation.col_notify') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($donorRows as $i => $row)
                        {{-- Main row --}}
                        <tr wire:click="toggleRow({{ $row['id'] }})"
                            class="cursor-pointer transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/40 {{ $row['notify'] ? 'bg-green-50/30 dark:bg-green-900/10' : '' }}">

                            {{-- # --}}
                            <td class="py-3 px-4 text-xs text-gray-400 dark:text-gray-500">{{ $i + 1 }}</td>

                            {{-- Donor Name --}}
                            <td class="py-3 px-4 font-medium text-gray-900 dark:text-white">
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-400 text-xs">{{ $expandedDonorId === $row['id'] ? '▼' : '▶' }}</span>
                                    {{ $row['name'] }}
                                </div>
                            </td>

                            {{-- Blood Type --}}
                            <td class="py-3 px-4">
                                <span class="inline-flex rounded-full bg-red-100 dark:bg-red-900/40 px-2.5 py-0.5 text-xs font-semibold text-red-700 dark:text-red-300">
                                    {{ $row['blood_type'] }}
                                </span>
                            </td>

                            {{-- Distance --}}
                            <td class="py-3 px-4 text-gray-600 dark:text-gray-300">
                                {{ $row['distance'] !== '—' ? $row['distance'] . ' كم' : '—' }}
                            </td>

                            {{-- Score Bar --}}
                            <td class="py-3 px-4">
                                @php
                                    $pct      = (int) round($row['score'] * 100);
                                    $barColor = $row['score'] >= 0.7
                                        ? 'bg-green-500'
                                        : ($row['score'] >= 0.4 ? 'bg-yellow-400' : 'bg-red-400');
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="relative h-2 flex-1 rounded-full bg-gray-100 dark:bg-gray-700">
                                        <div class="absolute inset-y-0 start-0 rounded-full {{ $barColor }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="w-8 text-end text-xs font-mono text-gray-600 dark:text-gray-300">{{ $pct }}%</span>
                                </div>
                            </td>

                            {{-- Source Badge --}}
                            <td class="py-3 px-4">
                                @php
                                    [$srcLabel, $srcClass] = match($row['source']) {
                                        'db_cache'   => ['db_cache',   'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'],
                                        'fastapi'    => ['fastapi',    'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'],
                                        'rule_based' => ['rule_based', 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'],
                                        default      => [$row['source'], 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300'],
                                    };
                                @endphp
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $srcClass }}">{{ $srcLabel }}</span>
                            </td>

                            {{-- Bucket --}}
                            <td class="py-3 px-4 text-sm">
                                @if($row['bucket'] === 'exploitation')
                                    <span class="text-blue-600 dark:text-blue-400">🔵 {{ __('filament.pages.donor-scoring-simulation.exploitation') }}</span>
                                @else
                                    <span class="text-yellow-600 dark:text-yellow-400">🟡 {{ __('filament.pages.donor-scoring-simulation.exploration') }}</span>
                                @endif
                            </td>

                            {{-- Notify --}}
                            <td class="py-3 px-4 text-center">
                                @if($row['notify'])
                                    <span class="font-bold text-green-600 dark:text-green-400" title="{{ __('filament.pages.donor-scoring-simulation.will_notify') }}">✅</span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600" title="{{ __('filament.pages.donor-scoring-simulation.wont_notify') }}">❌</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Expanded details row --}}
                        @if($expandedDonorId === $row['id'])
                        <tr class="bg-gray-50 dark:bg-gray-800/60">
                            <td colspan="8" class="px-6 py-4">
                                @php
                                    $acceptRate = $row['total_responses'] > 0
                                        ? round(($row['accepted_count'] / $row['total_responses']) * 100)
                                        : null;
                                    $lastActive = $row['last_responded_at']
                                        ? \Carbon\Carbon::parse($row['last_responded_at'])->diffForHumans()
                                        : null;
                                    $mv = $row['model_version'] ?? null;
                                    $mvColor = $mv && (str_contains($mv, 'xgboost') || str_contains($mv, 'fastapi'))
                                        ? 'text-blue-500 dark:text-blue-400'
                                        : 'text-orange-500 dark:text-orange-400';
                                @endphp
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">

                                    {{-- Total Responses --}}
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('filament.pages.donor-scoring-simulation.detail_total_responses') }}</div>
                                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $row['total_responses'] }}</div>
                                        @if($row['no_show_count'] > 0)
                                            <div class="text-xs text-red-400 mt-0.5">{{ $row['no_show_count'] }} no-show</div>
                                        @endif
                                    </div>

                                    {{-- Acceptance Rate --}}
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('filament.pages.donor-scoring-simulation.detail_acceptance_rate') }}</div>
                                        @if($acceptRate !== null)
                                            <div class="text-xl font-bold {{ $acceptRate >= 60 ? 'text-green-600 dark:text-green-400' : ($acceptRate >= 30 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-500 dark:text-red-400') }}">
                                                {{ $acceptRate }}%
                                            </div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $row['accepted_count'] }}/{{ $row['total_responses'] }}</div>
                                        @else
                                            <div class="text-xl font-bold text-gray-300 dark:text-gray-600">—</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ __('filament.pages.donor-scoring-simulation.detail_no_history') }}</div>
                                        @endif
                                    </div>

                                    {{-- Total Donations --}}
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('filament.pages.donor-scoring-simulation.detail_total_donations') }}</div>
                                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $row['total_donations'] }}</div>
                                    </div>

                                    {{-- Last Active / Model Version --}}
                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mb-1">{{ __('filament.pages.donor-scoring-simulation.detail_last_active') }}</div>
                                        <div class="text-sm font-medium text-gray-700 dark:text-gray-200">
                                            {{ $lastActive ?? '—' }}
                                        </div>
                                        @if($row['source'] === 'db_cache' && $mv)
                                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                model: <span class="font-semibold {{ $mvColor }}">{{ $mv }}</span>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </td>
                        </tr>
                        @endif

                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        @else
        {{-- Empty state --}}
        <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
            <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">{{ __('filament.pages.donor-scoring-simulation.no_eligible_donors') }}</p>
        </div>
        @endif

        {{-- Dry-run disclaimer --}}
        <p class="text-center text-xs text-gray-400 dark:text-gray-500">
            ⚠️ {{ __('filament.pages.donor-scoring-simulation.disclaimer') }}
        </p>

    @endif

</x-filament-panels::page>
