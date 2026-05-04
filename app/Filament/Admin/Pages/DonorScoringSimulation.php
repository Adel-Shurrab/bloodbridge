<?php

namespace App\Filament\Admin\Pages;

use App\DataTransferObjects\ScoringResult;
use App\Enums\RequestResponseStatus;
use App\Enums\UrgencyLevel;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Services\BloodRequestBroadcastService;
use App\Services\DonorScoringService;
use App\Settings\ScoringSettings;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DonorScoringSimulation extends Page
{
    protected string $view = 'filament.pages.donor-scoring-simulation';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static ?int $navigationSort = 4;

    // ── Livewire state ─────────────────────────────────────────────────────

    public ?int $bloodRequestId = null;

    public bool $hasRun = false;

    public array $summaryCards = [];

    public array $donorRows = [];

    public ?int $expandedDonorId = null;

    // ── Navigation / title ─────────────────────────────────────────────────

    public static function getNavigationLabel(): string
    {
        return __('filament.pages.donor-scoring-simulation.nav_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.system-reports');
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('filament.pages.donor-scoring-simulation.title');
    }

    // ── Toggle row expansion ───────────────────────────────────────────────

    public function toggleRow(int $donorId): void
    {
        $this->expandedDonorId = $this->expandedDonorId === $donorId ? null : $donorId;
    }

    // ── Dropdown data ──────────────────────────────────────────────────────

    public function getBloodRequestOptions(): array
    {
        return BloodRequest::with('organization')
            ->orderByDesc('created_at')
            ->get()
            ->mapWithKeys(function (BloodRequest $r) {
                $org    = $r->organization?->name ?? __('admin.no_organization');
                $blood  = $r->blood_type?->getLabel() ?? '?';
                $units  = $r->units_needed;
                $status = $r->status->getLabel();
                $date   = $r->created_at->format('Y-m-d');

                return [$r->id => "#{$r->id} — {$org} | {$blood} × {$units} | {$status} | {$date}"];
            })
            ->toArray();
    }

    // ── Main simulation ────────────────────────────────────────────────────

    public function runSimulation(): void
    {
        $this->validate(['bloodRequestId' => 'required|exists:blood_requests,id']);

        $this->expandedDonorId = null;

        $bloodRequest = BloodRequest::with('organization')->findOrFail($this->bloodRequestId);

        /** @var BloodRequestBroadcastService $broadcastSvc */
        $broadcastSvc   = app(BloodRequestBroadcastService::class);
        $eligibleDonors = $broadcastSvc->getEligibleDonors($bloodRequest);

        if ($eligibleDonors->isEmpty()) {
            $this->hasRun       = true;
            $this->summaryCards = $this->buildEmptySummary(0);
            $this->donorRows    = [];

            Notification::make()
                ->title(__('filament.pages.donor-scoring-simulation.simulation_complete'))
                ->warning()
                ->send();

            return;
        }

        // Cast to Eloquent Collection to enable eager-loading
        $eligibleDonors = new \Illuminate\Database\Eloquent\Collection($eligibleDonors->all());
        $eligibleDonors->load('user', 'healthProfile');

        $urgency = $bloodRequest->urgency_level === UrgencyLevel::CRITICAL ? 'critical' : 'normal';

        /** @var DonorScoringService $scoringSvc */
        $scoringSvc = app(DonorScoringService::class);
        $result     = $scoringSvc->scoreAndSelect($eligibleDonors, $urgency);

        $settings = app(ScoringSettings::class);
        $budget   = $settings->max_notifications_per_broadcast;
        if ($urgency === 'critical') {
            $budget = (int) ($budget * 1.5);
        }

        $selectedIds = $result['selected']->pluck('id')->flip();

        // Fetch response stats and cached model versions in one query each
        $donorIds      = $eligibleDonors->pluck('id')->all();
        $stats         = $this->fetchResponseStats($donorIds);
        $modelVersions = $this->fetchModelVersions($donorIds);

        $this->donorRows = $eligibleDonors
            ->map(function (Donor $donor) use ($selectedIds, $stats, $modelVersions) {
                /** @var ScoringResult $sr */
                $sr = $donor->getAttribute('scoringResult');

                $healthProfile = $donor->healthProfile;
                $bloodType     = $healthProfile?->verified_blood_type ?? $healthProfile?->blood_type;
                $donorStats    = $stats[$donor->id] ?? null;
                $modelVersion  = $modelVersions[$donor->id] ?? null;

                return [
                    'id'         => $donor->id,
                    'name'       => $donor->user?->name ?? '—',
                    'blood_type' => $bloodType instanceof \App\Enums\BloodType
                        ? $bloodType->getLabel()
                        : '—',
                    'distance'   => $donor->getAttribute('distance') !== null
                        ? number_format((float) $donor->getAttribute('distance'), 1)
                        : '—',
                    'score'      => $sr->score,
                    'source'     => $sr->source,
                    'bucket'     => $donor->getAttribute('scoringBucket') ?? 'exploration',
                    'notify'     => $selectedIds->has($donor->id),
                    // Interaction stats
                    'total_responses'  => $donorStats?->total_responses ?? 0,
                    'accepted_count'   => $donorStats?->accepted_count ?? 0,
                    'no_show_count'    => $donorStats?->no_show_count ?? 0,
                    'total_donations'  => $healthProfile?->total_donations ?? 0,
                    'last_responded_at'=> $donorStats?->last_responded_at,
                    'model_version'    => $modelVersion,
                ];
            })
            ->sortByDesc('score')
            ->values()
            ->toArray();

        $this->summaryCards = [
            'total_eligible'    => $eligibleDonors->count(),
            'exploitation_pool' => $result['exploiter_count'],
            'exploration_pool'  => $result['explorer_count'],
            'budget'            => $budget,
            'selected'          => $result['selected']->count(),
            'cold_start'        => $result['cold_start_count'],
            'source_breakdown'  => $result['source_breakdown'],
        ];

        $this->hasRun = true;

        Notification::make()
            ->title(__('filament.pages.donor-scoring-simulation.simulation_complete'))
            ->success()
            ->send();
    }

    /**
     * Fetch response stats for multiple donors in a single query.
     *
     * @param  int[] $donorIds
     * @return array<int, object>
     */
    private function fetchResponseStats(array $donorIds): array
    {
        $acceptedValue  = RequestResponseStatus::ACCEPTED->value;
        $completedValue = RequestResponseStatus::COMPLETED->value;
        $noShowValue    = RequestResponseStatus::NO_SHOW->value;

        $countedStatuses = [
            RequestResponseStatus::PENDING->value,
            RequestResponseStatus::ACCEPTED->value,
            RequestResponseStatus::COMPLETED->value,
            RequestResponseStatus::IGNORED->value,
            RequestResponseStatus::NO_SHOW->value,
            RequestResponseStatus::UNREACHABLE->value,
            RequestResponseStatus::NOT_NEEDED->value,
        ];

        return DB::table('request_responses')
            ->select([
                'donor_id',
                DB::raw('COUNT(*) as total_responses'),
                DB::raw("COUNT(CASE WHEN status IN ({$acceptedValue}, {$completedValue}) THEN 1 END) as accepted_count"),
                DB::raw("COUNT(CASE WHEN status = {$noShowValue} THEN 1 END) as no_show_count"),
                DB::raw('MAX(responded_at) as last_responded_at'),
            ])
            ->whereIn('donor_id', $donorIds)
            ->whereIn('status', $countedStatuses)
            ->whereNotNull('responded_at')
            ->groupBy('donor_id')
            ->get()
            ->keyBy('donor_id')
            ->toArray();
    }

    /**
     * Fetch model_version from donor_predictive_scores for db_cache display.
     *
     * @param  int[] $donorIds
     * @return array<int, string|null>
     */
    private function fetchModelVersions(array $donorIds): array
    {
        return DB::table('donor_predictive_scores')
            ->whereIn('donor_id', $donorIds)
            ->pluck('model_version', 'donor_id')
            ->toArray();
    }

    private function buildEmptySummary(int $budget): array
    {
        return [
            'total_eligible'    => 0,
            'exploitation_pool' => 0,
            'exploration_pool'  => 0,
            'budget'            => $budget,
            'selected'          => 0,
            'cold_start'        => 0,
            'source_breakdown'  => [],
        ];
    }
}
