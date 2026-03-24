@php
    $data = $this->getData();

    $aucStyles = match ($data['aucColor']) {
        'success' => [
            'card' => 'from-success-50 to-success-100 dark:from-success-950 dark:to-success-900',
            'orb' => 'bg-success-200 dark:bg-success-800',
            'text' => 'text-success-700 dark:text-success-300',
            'badge' => 'bg-success-700',
            'iconWrap' => 'bg-success-200 dark:bg-success-800',
            'label' => __('admin.excellent'),
        ],
        'warning' => [
            'card' => 'from-warning-50 to-warning-100 dark:from-warning-950 dark:to-warning-900',
            'orb' => 'bg-warning-200 dark:bg-warning-800',
            'text' => 'text-warning-700 dark:text-warning-300',
            'badge' => 'bg-warning-700',
            'iconWrap' => 'bg-warning-200 dark:bg-warning-800',
            'label' => __('admin.good'),
        ],
        default => [
            'card' => 'from-danger-50 to-danger-100 dark:from-danger-950 dark:to-danger-900',
            'orb' => 'bg-danger-200 dark:bg-danger-800',
            'text' => 'text-danger-700 dark:text-danger-300',
            'badge' => 'bg-danger-700',
            'iconWrap' => 'bg-danger-200 dark:bg-danger-800',
            'label' => __('admin.needs_improvement'),
        ],
    };

    $circuitStyles = match ($data['circuitColor']) {
        'success' => [
            'card' => 'border-success-200 dark:border-success-700 bg-success-50 dark:bg-success-900/30',
            'dot' => 'bg-success-500',
            'text' => 'text-success-700 dark:text-success-300',
        ],
        'warning' => [
            'card' => 'border-warning-200 dark:border-warning-700 bg-warning-50 dark:bg-warning-900/30',
            'dot' => 'bg-warning-500',
            'text' => 'text-warning-700 dark:text-warning-300',
        ],
        default => [
            'card' => 'border-danger-200 dark:border-danger-700 bg-danger-50 dark:bg-danger-900/30',
            'dot' => 'bg-danger-500',
            'text' => 'text-danger-700 dark:text-danger-300',
        ],
    };
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex w-full items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900">
                        <x-filament::icon icon="heroicon-o-cpu-chip"
                            class="h-6 w-6 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('admin.ml_scoring_system') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('admin.performance_health') }}</p>
                    </div>
                </div>
                @if ($data['mlEnabled'])
                    <x-filament::badge color="success" icon="heroicon-o-check-circle">{{ __('admin.active') }}</x-filament::badge>
                @else
                    <x-filament::badge color="gray" icon="heroicon-o-x-circle">{{ __('admin.disabled') }}</x-filament::badge>
                @endif
            </div>
        </x-slot>

        <div class="mb-8 grid gap-6 md:grid-cols-2">
            <div
                class="relative overflow-hidden rounded-2xl border border-gray-200 bg-gradient-to-br p-6 dark:border-gray-700 {{ $aucStyles['card'] }}">
                <div class="absolute right-0 top-0 h-32 w-32 rounded-full opacity-20 blur-3xl {{ $aucStyles['orb'] }}">
                </div>

                <div class="relative z-10">
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                {{ __('admin.auc_roc_score') }}</p>
                            <p class="mt-2 text-4xl font-bold {{ $aucStyles['text'] }}">{{ $data['aucRoc'] }}</p>
                        </div>
                        <div class="rounded-full p-3 {{ $aucStyles['iconWrap'] }}">
                            <x-filament::icon icon="heroicon-o-chart-bar" class="h-6 w-6 {{ $aucStyles['text'] }}" />
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300">
                        <span class="inline-block h-2 w-2 rounded-full {{ $aucStyles['badge'] }}"></span>
                        {{ $aucStyles['label'] }} - {{ __('admin.target') }}: &gt; 0.72
                    </div>
                </div>
            </div>

            <div
                class="relative overflow-hidden rounded-2xl border border-gray-200 bg-gradient-to-br from-blue-50 to-blue-100 p-6 dark:border-gray-700 dark:from-blue-950 dark:to-blue-900">
                <div
                    class="absolute right-0 top-0 h-32 w-32 rounded-full bg-blue-200 opacity-20 blur-3xl dark:bg-blue-800">
                </div>

                <div class="relative z-10">
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300">
                                {{ __('admin.acceptance_rate') }}</p>
                            <p class="mt-2 text-4xl font-bold text-blue-700 dark:text-blue-300">
                                {{ $data['acceptanceRate'] }}</p>
                        </div>
                        <div class="rounded-full bg-blue-200 p-3 dark:bg-blue-800">
                            <x-filament::icon icon="heroicon-o-hand-thumb-up"
                                class="h-6 w-6 text-blue-700 dark:text-blue-300" />
                        </div>
                    </div>
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('admin.last_7_days') }}</p>
                </div>
            </div>
        </div>

        <div class="mb-8 grid gap-4 md:grid-cols-4">
            <div
                class="rounded-xl border border-gray-200 bg-gray-50 p-4 transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800/50">
                <div class="mb-3 flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-cube" class="h-4 w-4 text-purple-600 dark:text-purple-400" />
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ __('admin.model_version') }}</p>
                </div>
                <p class="text-base font-bold text-gray-900 dark:text-white">{{ $data['modelVersion'] }}</p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('admin.trained') }}: <span
                        class="font-medium">{{ $data['lastTrained'] }}</span></p>
            </div>

            <div
                class="rounded-xl border border-gray-200 bg-gray-50 p-4 transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800/50">
                <div class="mb-3 flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-rectangle-stack"
                        class="h-4 w-4 text-orange-600 dark:text-orange-400" />
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ __('admin.training_data') }}</p>
                </div>
                <p class="text-base font-bold text-gray-900 dark:text-white">{{ number_format($data['dataRecords']) }}
                </p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('admin.records') }}</p>
            </div>

            <div
                class="rounded-xl border border-gray-200 bg-gray-50 p-4 transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800/50">
                <div class="mb-3 flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-sparkles" class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ __('admin.exploration_epsilon') }}
                    </p>
                </div>
                <p class="text-base font-bold text-gray-900 dark:text-white">{{ $data['epsilon'] }}</p>
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    @if ($data['elapsedDays'] !== null)
                        <span class="font-medium">{{ __('admin.day') }} {{ $data['elapsedDays'] }}</span>
                    @else
                        {{ __('admin.not_activated') }}
                    @endif
                </p>
            </div>

            <div class="rounded-xl border p-4 transition-shadow hover:shadow-md {{ $circuitStyles['card'] }}">
                <div class="mb-3 flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full animate-pulse {{ $circuitStyles['dot'] }}"></div>
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ __('admin.fastapi') }}</p>
                </div>
                <p class="text-xs font-medium {{ $circuitStyles['text'] }}">
                    {{ str_replace('FastAPI Circuit Breaker: ', '', $data['circuitLabel']) }}
                </p>
            </div>
        </div>

        <div class="mb-8">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ __('admin.acceptance_rate_trend') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('admin.last_7_days_title') }}</p>
                </div>
                <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5 text-gray-400" />
            </div>

            <div
                class="rounded-xl border border-gray-200 bg-gradient-to-br from-gray-50 to-gray-100 p-6 dark:border-gray-700 dark:from-gray-800 dark:to-gray-900">
                <div class="flex h-32 items-end justify-between gap-3">
                    @foreach ($data['chartData'] as $day)
                        <div class="group flex flex-1 flex-col items-center gap-2">
                            <span
                                class="text-xs font-semibold text-gray-700 transition-colors group-hover:text-primary-600 dark:text-gray-300 dark:group-hover:text-primary-400">
                                {{ $day['rate'] }}%
                            </span>
                            <div class="flex w-full flex-col items-center">
                                <div class="w-full rounded-lg bg-gradient-to-t from-primary-500 to-primary-400 transition-all hover:shadow-lg group-hover:from-primary-600 group-hover:to-primary-500 dark:from-primary-600 dark:to-primary-500"
                                    style="height: {{ max($day['rate'], 8) }}px; min-height: 8px;"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $day['date'] }}</span>
                            <span
                                class="text-xs text-gray-500 opacity-0 transition-opacity group-hover:opacity-100 dark:text-gray-500">{{ $day['total'] }}
                                {{ __('admin.qty') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
            <p class="text-xs text-gray-600 dark:text-gray-400">
                {{ __('admin.last_updated') }}: <span class="font-medium">{{ now()->format('H:i:s') }}</span>
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
