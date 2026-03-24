<?php

namespace App\Filament\Donor\Pages;

use App\Enums\BloodType;
use App\Enums\RequestResponseStatus;
use App\Enums\UrgencyLevel;
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
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Concerns\HasActiveLocaleSwitcher;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BloodRequests extends Page implements HasTable
{
    use InteractsWithTable, HasActiveLocaleSwitcher {
        HasActiveLocaleSwitcher::getActiveFormsLocale insteadof InteractsWithTable;
        HasActiveLocaleSwitcher::getActiveActionsLocale insteadof InteractsWithTable;
        HasActiveLocaleSwitcher::getFilamentTranslatableContentDriver insteadof InteractsWithTable;
    }

    public ?string $activeLocale = null;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-m-megaphone';

    protected ?Collection $donorResponses = null;

    protected ?Donor $cachedDonor = null;

    protected bool $donorLoaded = false;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.donor.pages.blood-requests';

    public static function getNavigationLabel(): string
    {
        return __('donor.blood_requests');
    }

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

    protected function getDonor(): ?Donor
    {
        if (! $this->donorLoaded) {
            $this->cachedDonor = Auth::user()?->donor?->load('healthProfile');
            $this->donorLoaded = true;
        }

        return $this->cachedDonor;
    }

    protected function getDonorBloodType(): ?BloodType
    {
        $profile = $this->getDonor()?->healthProfile;

        return $profile?->verified_blood_type ?? $profile?->blood_type;
    }

    protected function getDonorId(): ?int
    {
        return $this->getDonor()?->id;
    }

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

    protected function getRequestsQuery(): Builder | \Illuminate\Database\Query\Builder
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
                            ->whereRaw(
                                "{$haversine} <= COALESCE(blood_requests.actual_search_radius_km, blood_requests.search_radius_km)",
                                [$donor->lat, $donor->lng, $donor->lat],
                            );
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
                    ->label(__('donor.urgency_level'))
                    ->options(UrgencyLevel::class),
                SelectFilter::make('blood_type')
                    ->label(__('donor.blood_type'))
                    ->options(BloodType::class),
                SelectFilter::make('organization')
                    ->label(__('donor.organization'))
                    ->relationship('organization', 'org_name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    private function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('organization.org_name')
                ->label(__('donor.organization'))
                ->searchable()
                ->sortable()
                ->formatStateUsing(
                    fn($state, $record) => $record->organization?->getTranslation('org_name', app()->getLocale(), false)
                        ?? ($record->organization?->getTranslation('org_name', 'ar', false) ?? '-')
                ),
            Tables\Columns\TextColumn::make('distance_km')
                ->label(__('donor.distance'))
                ->state(
                    fn($record) => isset($record->distance_km)
                        ? number_format((float) $record->distance_km, 1) . ' ' . __('organization.km')
                        : '-'
                ),
            Tables\Columns\TextColumn::make('blood_type')
                ->label(__('donor.required_blood_type'))
                ->badge(),
            Tables\Columns\TextColumn::make('units_needed')
                ->label(__('donor.required_units'))
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->label(__('donor.request_status'))
                ->badge(),
            Tables\Columns\TextColumn::make('my_status')
                ->label(__('donor.your_status'))
                ->badge()
                ->getStateUsing(fn(BloodRequest $record) => $this->getDonorResponseForRequest($record)?->status)
                ->formatStateUsing(fn($state) => $state ? $state->getLabel() : __('donor.no_response'))
                ->color(fn($state) => $state ? $state->getColor() : 'gray'),
        ];
    }

    private function getTableActions(): array
    {
        return [
            Action::make('accept')
                ->label(__('donor.accept'))
                ->icon('heroicon-m-check-circle')
                ->visible(fn(BloodRequest $record) => $this->canAccept($record))
                ->disabled(fn() => ! $this->isEligibleNow())
                ->action(fn(BloodRequest $record, BloodRequestActionService $service) => $this->accept($record, $service)),
            Action::make('ignore')
                ->label(__('donor.decline'))
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->visible(fn(BloodRequest $record) => $this->canIgnore($record))
                ->action(fn(BloodRequest $record, BloodRequestActionService $service) => $this->ignore($record, $service)),
            Action::make('cancel')
                ->label(__('donor.cancel_acceptance'))
                ->color('gray')
                ->icon('heroicon-m-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading(__('donor.confirm_cancellation'))
                ->modalDescription(__('donor.cancel_acceptance_confirmation'))
                ->modalSubmitActionLabel(__('donor.yes_cancel'))
                ->visible(fn(BloodRequest $record) => $this->canCancel($record))
                ->action(fn(BloodRequest $record, BloodRequestActionService $service) => $this->cancel($record, $service)),
            Action::make('download_qr')
                ->label(__('donor.download_qr'))
                ->icon('heroicon-m-qr-code')
                ->visible(fn(BloodRequest $record) => $this->canDownloadQr($record))
                ->action(fn(BloodRequest $record, QRCodeService $qrService) => $this->downloadQr($record, $qrService)),
        ];
    }

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

    protected function getDonorResponseForRequest(BloodRequest $request): ?RequestResponse
    {
        return $this->getDonorResponses()->get($request->id);
    }

    protected function requestIsActive(BloodRequest $request): bool
    {
        return $request->isActive();
    }

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

    protected function canCancel(BloodRequest $request): bool
    {
        if (! $this->requestIsActive($request)) {
            return false;
        }

        $response = $this->getDonorResponseForRequest($request);

        return $response && $response->status === RequestResponseStatus::PENDING;
    }

    protected function accept(BloodRequest $request, BloodRequestActionService $service): void
    {
        $donor = $this->getDonor();

        if (! $donor) {
            Notification::make()->danger()->title(__('donor.unable_to_determine_donor_data'))->send();
            return;
        }

        try {
            $service->accept($donor, $request);

            $this->donorResponses = null;

            Notification::make()
                ->success()
                ->title(__('donor.request_accepted'))
                ->body(__('donor.download_qr_and_present_to_organization'))
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('donor.an_error_occurred'))
                ->body($e->getMessage())
                ->send();
        }
    }

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
                ->title(__('donor.request_declined'))
                ->send();
        } catch (\Exception $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
    }

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
                ->title(__('donor.successfully_cancelled'))
                ->send();
        } catch (\Exception $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
    }

    protected function downloadQr(BloodRequest $request, QRCodeService $qrService): ?StreamedResponse
    {
        $response = $this->getDonorResponseForRequest($request);

        if (! $response || ! $this->canDownloadQr($request)) {
            Notification::make()->danger()->title(__('donor.unable_to_download_qr_at_this_time'))->send();
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
