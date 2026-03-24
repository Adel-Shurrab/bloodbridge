<?php

namespace App\Filament\Admin\Widgets;

use App\Models\ModelTrainingLog;
use App\Models\RequestResponse;
use App\Services\DonorScoringService;
use App\Settings\ScoringSettings;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class MLScoringMonitorWidget extends Widget
{
    use CanPoll;

    protected string $view = 'filament.widgets.ml-scoring-monitor';

    protected static ?int $sort = 99;

    protected int | string | array $columnSpan = 'full';

    public function getData(): array
    {
        $settings = app(ScoringSettings::class);
        $latest = ModelTrainingLog::latest('training_date')->first();

        $aucRoc = $latest?->metrics['auc_roc'] ?? null;
        $aucColor = match (true) {
            $aucRoc === null => 'gray',
            $aucRoc >= 0.72 => 'success',
            $aucRoc >= 0.65 => 'warning',
            default => 'danger',
        };

        $last7Total = RequestResponse::where('created_at', '>=', now()->subDays(7))
            ->whereIn('status', [1, 2, 3, 4, 5, 6, 7])
            ->count();

        $last7Accepted = RequestResponse::where('created_at', '>=', now()->subDays(7))
            ->whereIn('status', [1, 3])
            ->count();

        $acceptanceRate = $last7Total > 0
            ? round(($last7Accepted / $last7Total) * 100, 1)
            : null;

        $circuitState = Cache::get('fastapi_circuit:state', 'closed');
        $circuitColor = match ($circuitState) {
            'closed' => 'success',
            'half_open' => 'warning',
            'open' => 'danger',
            default => 'gray',
        };

        $circuitLabel = match ($circuitState) {
            'closed' => __('admin.closed_fastapi_healthy'),
            'half_open' => __('admin.half_open_testing_recovery'),
            'open' => __('admin.open_fastapi_unreachable'),
            default => __('admin.unknown'),
        };

        $epsilon = $settings->exploration_ratio;
        $enabledSince = $settings->ml_enabled_since
            ? Carbon::parse($settings->ml_enabled_since)->diffInDays(now())
            : null;

        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $total = RequestResponse::whereDate('created_at', $date)
                ->whereIn('status', [1, 2, 3, 4, 5, 6, 7])
                ->count();
            $accepted = RequestResponse::whereDate('created_at', $date)
                ->whereIn('status', [1, 3])
                ->count();

            $chartData[] = [
                'date' => now()->subDays($i)->translatedFormat('M d'),
                'rate' => $total > 0 ? round(($accepted / $total) * 100, 1) : 0,
                'total' => $total,
            ];
        }

        return [
            'mlEnabled' => $settings->ml_scoring_enabled,
            'modelVersion' => $latest?->model_version ?? __('admin.no_model_yet'),
            'lastTrained' => $latest?->training_date?->diffForHumans() ?? __('admin.never'),
            'dataRecords' => $latest?->data_records_used ?? 0,
            'aucRoc' => $aucRoc ? number_format($aucRoc, 3) : __('admin.not_available_short'),
            'aucColor' => $aucColor,
            'acceptanceRate' => $acceptanceRate ? $acceptanceRate . '%' : __('admin.no_data'),
            'circuitState' => $circuitState,
            'circuitColor' => $circuitColor,
            'circuitLabel' => $circuitLabel,
            'epsilon' => number_format($epsilon * 100, 0) . '%',
            'elapsedDays' => $enabledSince !== null ? (int) $enabledSince : null,
            'chartData' => $chartData,
        ];
    }


}
