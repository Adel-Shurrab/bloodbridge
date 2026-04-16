<?php

namespace App\Filament\Admin\Pages;

use App\DataTransferObjects\ScoringResult;
use App\Enums\UrgencyLevel;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Services\BloodRequestBroadcastService;
use App\Services\DonorScoringService;
use App\Settings\ScoringSettings;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

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

        // Eager-load relationships to avoid N+1 when building rows
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

        /** @var Collection $selected */
        $selectedIds = $result['selected']->pluck('id')->flip();

        $this->donorRows = $eligibleDonors
            ->map(function (Donor $donor) use ($selectedIds) {
                /** @var ScoringResult $sr */
                $sr = $donor->getAttribute('scoringResult');

                $healthProfile = $donor->healthProfile;
                $bloodType     = $healthProfile?->verified_blood_type ?? $healthProfile?->blood_type;

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
