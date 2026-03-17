<?php

namespace App\Filament\Donor\Pages;

use App\Enums\BloodType;
use App\Enums\RequestResponseStatus;
use App\Filament\Donor\Widgets\EligibilityCountdownWidget;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\RequestResponse;
use App\Services\BloodRequestActionService;
use App\Services\QRCodeService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Enums\UrgencyLevel;
use LaraZeus\SpatieTranslatable\Resources\Concerns\HasActiveLocaleSwitcher;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class BloodRequests extends Page implements HasTable
{
    use InteractsWithTable, HasActiveLocaleSwitcher {
        HasActiveLocaleSwitcher::getActiveFormsLocale insteadof InteractsWithTable;
        HasActiveLocaleSwitcher::getActiveActionsLocale insteadof InteractsWithTable;
        HasActiveLocaleSwitcher::getFilamentTranslatableContentDriver insteadof InteractsWithTable;
    }

    public ?string $activeLocale = null;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-m-megaphone';
    protected ?Collection $donorResponses = null;
    protected ?Donor $cachedDonor = null;
    protected bool $donorLoaded = false;
    public static function getNavigationLabel(): string
    {
        return __('Blood Requests');
    }
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.donor.pages.blood-requests';

    public function getHeading(): string
    {
        return '';
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var static $page */
        $page = new static();

        $count = $page->getRequestsQuery()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EligibilityCountdownWidget::class,
        ];
    }

    /**
     * Get the authenticated donor with health profile eager-loaded.
     *
     * Uses once() to avoid redundant queries across the page lifecycle.
     */
    protected function getDonor(): ?Donor
    {
        if (! $this->donorLoaded) {
            $this->cachedDonor = Auth::user()?->donor?->load('healthProfile');
            $this->donorLoaded = true;
        }

        return $this->cachedDonor;
    }

    /**
     * Resolve the donor's effective blood type (verified takes priority).
     */
    protected function getDonorBloodType(): ?BloodType
    {
        $profile = $this->getDonor()?->healthProfile;

        return $profile?->verified_blood_type ?? $profile?->blood_type;
    }

    /**
     * Get the authenticated donor ID, or null if unavailable.
     */
    protected function getDonorId(): ?int
    {
        return $this->getDonor()?->id;
    }

    /**
     * Check if the donor is currently eligible to donate.
     */
    protected function isEligibleNow(): bool
    {
        $profile = $this->getDonor()?->healthProfile;

        if (! $profile) {
            return false;
        }

        if ($profile->is_eligible) {
            return true;
        }

        if ($profile->next_eligible_date === null) {
            
            return false;
        }

        return $profile->next_eligible_date->startOfDay()->isPast()
            || $profile->next_eligible_date->startOfDay()->isToday();
    }

    /**
     * Build the query for blood requests visible to this donor.
     *
     * Filters:
     *  - status in [BROADCASTED, MATCHED] (via scopeActive)
     *  - not yet fulfilled
     *  - compatible blood type (or all if UNKNOWN / no profile)
     */
    protected function getRequestsQuery(): Builder|\Illuminate\Database\Query\Builder
    {
        $donor = $this->getDonor();

        if (! $this->isEligibleNow()) {
            return BloodRequest::query()->whereRaw('0 = 1');
        }

        return BloodRequest::query()
            ->with('organization')
            ->active()
            ->compatibleWithDonor($this->getDonorBloodType())
            ->withDistance($donor?->lat, $donor?->lng)
            ->where(function ($query) use ($donor) {
                if ($donor && $donor->lat && $donor->lng) {
                    $haversine = "(
                        6371 * acos(
                            cos(radians(?)) * cos(radians(blood_requests.lat)) * cos(radians(blood_requests.lng) - radians(?)) +
                            sin(radians(?)) * sin(radians(blood_requests.lat))
                        )
                    )";

                    $query->where(function ($q) use ($haversine, $donor) {
                        $q->whereNotNull('blood_requests.lat')
                            ->whereNotNull('blood_requests.lng')
                            ->whereRaw("{$haversine} <= COALESCE(blood_requests.actual_search_radius_km, blood_requests.search_radius_km)", [$donor->lat, $donor->lng, $donor->lat]);
                    })->orWhere(function ($q) use ($donor) {
                        $q->whereNull('blood_requests.lat')
                            ->whereHas('organization', fn($org) => $org->where('governorate_id', $donor->governorate_id));
                    });
                } elseif ($donor) {
                    $query->whereHas('organization', fn($org) => $org->where('governorate_id', $donor->governorate_id));
                }
            })

            ->whereDoesntHave('responses', function ($query) use ($donor) {
                $query->where('donor_id', $donor->id)
                    ->where('status', '!=', RequestResponseStatus::PENDING);
            })
            ->orderBy('distance_km')
            ->orderByDesc('broadcasted_at');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getRequestsQuery())
            ->columns($this->getTableColumns())
            ->actions($this->getTableActions())
            ->filters([
                SelectFilter::make('urgency_level')
                    ->label(__('Urgency Level'))
                    ->options(UrgencyLevel::class),

                SelectFilter::make('blood_type')
                    ->label(__('Blood Type'))
                    ->options(BloodType::class),

                SelectFilter::make('organization')
                    ->label(__('Organization'))
                    ->relationship('organization', 'org_name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    /**
     * @return array<Tables\Columns\Column>
     */
    private function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('organization.org_name')
                ->label(__('Organization'))
                ->searchable()
                ->sortable()
                ->formatStateUsing(fn($state, $record) => $record->organization?->getTranslation('org_name', app()->getLocale(), false) ?? ($record->organization?->getTranslation('org_name', 'ar', false) ?? '—')),

            Tables\Columns\TextColumn::make('distance_km')
                ->label(__('Distance'))
                ->state(
                    fn($record) => isset($record->distance_km)
                        ? number_format((float) $record->distance_km, 1) . ' ' . __('km')
                        : '—'
                ),

            Tables\Columns\TextColumn::make('blood_type')
                ->label(__('Required Blood Type'))
                ->badge(),

            Tables\Columns\TextColumn::make('units_needed')
                ->label(__('Required Units'))
                ->numeric()
                ->sortable(),

            Tables\Columns\TextColumn::make('status')
                ->label(__('Request Status'))
                ->badge(),

            Tables\Columns\TextColumn::make('my_status')
                ->label(__('Your Status'))
                ->badge()
                ->getStateUsing(fn(BloodRequest $record) => $this->getDonorResponseForRequest($record)?->status)
                ->formatStateUsing(fn($state) => $state ? $state->getLabel() : __('No Response'))
                ->color(fn($state) => $state ? $state->getColor() : 'gray'),
        ];
    }

    /**
     * @return array<Action>
     */
    private function getTableActions(): array
    {
        return [
            Action::make('accept')
                ->label(__('Accept'))
                ->icon('heroicon-m-check-circle')
                ->visible(fn(BloodRequest $record) => $this->canAccept($record))
                ->disabled(fn() => ! $this->isEligibleNow())
                ->action(fn(BloodRequest $record, BloodRequestActionService $service) => $this->accept($record, $service)),

            Action::make('ignore')
                ->label(__('Decline'))
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->visible(fn(BloodRequest $record) => $this->canIgnore($record))
                ->action(fn(BloodRequest $record, BloodRequestActionService $service) => $this->ignore($record, $service)),

            Action::make('cancel')
                ->label(__('Cancel Acceptance'))
                ->color('gray')
                ->icon('heroicon-m-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading(__('Confirm Cancellation'))
                ->modalDescription(__('Are you sure you want to cancel your acceptance for this request? The current QR code will be invalidated.'))
                ->modalSubmitActionLabel(__('Yes, cancel'))
                ->visible(fn(BloodRequest $record) => $this->canCancel($record))
                ->action(fn(BloodRequest $record, BloodRequestActionService $service) => $this->cancel($record, $service)),

            Action::make('download_qr')
                ->label(__('Download QR'))
                ->icon('heroicon-m-qr-code')
                ->visible(fn(BloodRequest $record) => $this->canDownloadQr($record))
                ->action(fn(BloodRequest $record, QRCodeService $qrService) => $this->downloadQr($record, $qrService)),
        ];
    }

    /**
     * Get all responses for this donor, cached and indexed by blood_request_id.
     *
     * Eliminates N+1 queries by fetching all donor responses at once.
     */
    protected function getDonorResponses(): Collection
    {
        if ($this->donorResponses !== null) {
            return $this->donorResponses;
        }

        $donorId = $this->getDonorId();

        if (! $donorId) {
            return $this->donorResponses = collect();
        }

        return $this->donorResponses = RequestResponse::query()
            ->where('donor_id', $donorId)
            ->get()
            ->keyBy('blood_request_id');
    }

    /**
     * Get the donor's existing response for a specific blood request from the cached collection.
     */
    protected function getDonorResponseForRequest(BloodRequest $request): ?RequestResponse
    {
        return $this->getDonorResponses()->get($request->id);
    }

    /**
     * Check if the blood request is still active (not fulfilled or cancelled).
     */
    protected function requestIsActive(BloodRequest $request): bool
    {
        return $request->isActive();
    }

    /**
     * Donor can accept if the request is active, they haven't already accepted this one,
     * and they don't have a PENDING acceptance on any other request.
     */
    protected function canAccept(BloodRequest $request): bool
    {
        if (! $this->requestIsActive($request)) {
            return false;
        }

        $response = $this->getDonorResponseForRequest($request);

        if ($response && $response->status !== RequestResponseStatus::IGNORED) {
            return false;
        }

        $hasActivePending = $this->getDonorResponses()
            ->reject(fn(RequestResponse $r) => $r->blood_request_id === $request->id)
            ->contains(fn(RequestResponse $r) => $r->status === RequestResponseStatus::PENDING);

        return ! $hasActivePending;
    }

    /**
     * Donor can ignore if the request is active and they haven't already committed.
     */
    protected function canIgnore(BloodRequest $request): bool
    {
        if (! $this->requestIsActive($request)) {
            return false;
        }

        $response = $this->getDonorResponseForRequest($request);

        if (! $response) {
            return true;
        }

        if ($response->status === RequestResponseStatus::IGNORED) {
            return false;
        }

        return ! in_array($response->status, [
            RequestResponseStatus::PENDING,
            RequestResponseStatus::ACCEPTED,
            RequestResponseStatus::COMPLETED,
        ], true);
    }

    /**
     * Donor can download QR only when they have a pending, unexpired response.
     */
    protected function canDownloadQr(BloodRequest $request): bool
    {
        if (! $this->requestIsActive($request)) {
            return false;
        }

        $response = $this->getDonorResponseForRequest($request);

        return $response
            && $response->status === RequestResponseStatus::PENDING
            && $response->verification_qr_code
            && ! $response->verified_at
            && ! ($response->qr_code_expires_at?->isPast());
    }

    /**
     * Check if the donor can cancel their acceptance (only if PENDING).
     */
    protected function canCancel(BloodRequest $request): bool
    {
        if (! $this->requestIsActive($request)) {
            return false;
        }

        $response = $this->getDonorResponseForRequest($request);

        return $response && $response->status === RequestResponseStatus::PENDING;
    }

    /**
     * Accept a blood request — creates/updates the donor's response to PENDING.
     */
    protected function accept(BloodRequest $request, BloodRequestActionService $service): void
    {
        $donor = $this->getDonor();

        if (! $donor) {
            Notification::make()->danger()->title(__('Unable to determine donor data'))->send();
            return;
        }

        try {
            $service->accept($donor, $request);

            $this->donorResponses = null;

            Notification::make()
                ->success()
                ->title(__('Request Accepted'))
                ->body(__('You can now download the QR code and present it to the organization upon attendance.'))
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('An error occurred'))
                ->body($e->getMessage())
                ->send();
        }
    }

    /**
     * Ignore a blood request — sets the donor's response to IGNORED.
     */
    protected function ignore(BloodRequest $request, BloodRequestActionService $service): void
    {
        $donor = $this->getDonor();

        if (! $donor) {
            return;
        }

        try {
            $service->ignore($donor, $request);

            $this->donorResponses = null;

            Notification::make()
                ->success()
                ->title(__('Request Declined'))
                ->send();
        } catch (\Exception $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
    }

    /**
     * Cancel (undo) a previously accepted request.
     */
    protected function cancel(BloodRequest $request, BloodRequestActionService $service): void
    {
        $donor = $this->getDonor();

        if (! $donor) {
            return;
        }

        try {
            $service->cancel($donor, $request);

            $this->donorResponses = null;

            Notification::make()
                ->success()
                ->title(__('Successfully Cancelled'))
                ->send();
        } catch (\Exception $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
    }

    /**
     * Download the QR code image for a pending response.
     */
    protected function downloadQr(BloodRequest $request, QRCodeService $qrService): ?StreamedResponse
    {
        $response = $this->getDonorResponseForRequest($request);

        if (! $response || ! $this->canDownloadQr($request)) {
            Notification::make()->danger()->title(__('Unable to download QR at this time'))->send();
            return null;
        }

        $filename = 'bloodbridge-qr-' . $request->id . '.svg';

        return response()->streamDownload(function () use ($qrService, $response) {
            echo $qrService->render($response->verification_qr_code);
        }, $filename, [
            'Content-Type' => 'image/svg+xml',
        ]);
    }
}

