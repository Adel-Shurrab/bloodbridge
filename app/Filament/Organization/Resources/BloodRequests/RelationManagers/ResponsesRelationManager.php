<?php

namespace App\Filament\Organization\Resources\BloodRequests\RelationManagers;

use App\Models\RequestResponse;
use BackedEnum;
use App\Enums\BloodType;
use App\Enums\RequestResponseStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Grid as TableGrid;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use App\Models\EligibilityLog;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use App\Enums\BloodRequestStatus;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'Donor Responses';

    protected static string|BackedEnum|null $icon = 'heroicon-o-users';

    /**
     * Send a toast notification and dispatch it so it shows immediately in modals (Filament v4).
     */
    protected function sendToast(Notification $notification): void
    {
        $notification->send();
        $this->dispatch('notificationSent', $notification->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('Update Response Status'))
                    ->description(__('Choose the appropriate status for the response'))
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Select::make('status')
                            ->label(__('Status'))
                            ->options(RequestResponseStatus::class)
                            ->required()
                            ->native(false)
                            ->placeholder(__('Choose Status'))
                            ->helperText(__('The donor\'s response status will be updated')),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading(__('Responding Donors List'))
            ->description(__('View and manage donor responses for this request'))
            ->columns($this->getTableColumns())
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->filters($this->getTableFilters())
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->actions($this->getTableActions())
            ->bulkActions([])
            ->emptyStateHeading(__('No responses yet'))
            ->emptyStateDescription(__('No donors have responded to this request yet'))
            ->emptyStateIcon('heroicon-o-users')
            ->paginated([12, 24, 48, 'all'])
            ->defaultPaginationPageOption(12)
            ->defaultSort('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Stack::make([

                Split::make([
                    Stack::make([
                        TextColumn::make('donor.user.name')
                            ->label(__('Donor Name'))
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->searchable()
                            ->sortable()
                            ->icon('heroicon-m-user')
                            ->iconColor('primary'),

                        TextColumn::make('donor.user.phone')
                            ->label(__('Phone Number'))
                            ->icon('heroicon-m-phone')
                            ->color('gray')
                            ->size('sm')
                            ->default(__('None')),
                    ])->space(1),

                    TextColumn::make('status')
                        ->label(__('Status'))
                        ->badge()
                        ->size('md')
                        ->description(fn(RequestResponse $record) =>
                        !$record->donor->healthProfile?->is_eligible
                            ? __('Currently ineligible for donation')
                            : null)
                        ->grow(false),
                ]),

                Panel::make([
                    TableGrid::make(2)
                        ->schema([
                            Stack::make([
                                TextColumn::make('blood_type_header')
                                    ->label(__('Blood Type'))
                                    ->state(__('Blood Type'))
                                    ->size('xs')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextColumn::make('blood_type_display')
                                    ->state(
                                        fn(RequestResponse $record) =>
                                        $record->donor->healthProfile?->verified_blood_type?->getLabel() ??
                                            $record->donor->healthProfile?->blood_type?->getLabel() ??
                                            __('Not specified')
                                    )
                                    ->badge()
                                    ->size('lg')
                                    ->icon(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? 'heroicon-m-check-badge' : 'heroicon-m-question-mark-circle')
                                    ->color(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? 'success' : 'warning'),

                                TextColumn::make('blood_verification')
                                    ->state(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? __('✓ Lab Verified') : __('⚠ Self-reported'))
                                    ->size('xs')
                                    ->color(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? 'success' : 'warning')
                                    ->weight(FontWeight::SemiBold),
                            ])->space(1),

                            Stack::make([
                                TextColumn::make('timing_header')
                                    ->state(__('Timing'))
                                    ->size('xs')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextColumn::make('response_date')
                                    ->label(__('Response Date'))
                                    ->state(fn(RequestResponse $record) => $record->created_at->diffForHumans())
                                    ->icon('heroicon-m-clock')
                                    ->color('info')
                                    ->size('sm')
                                    ->weight(FontWeight::Medium),

                                TextColumn::make('connectivity_status')
                                    ->label(__('Connectivity Status'))
                                    ->badge()
                                    ->size('xs')
                                    ->visible(fn(?RequestResponse $record) => $record?->status === RequestResponseStatus::PENDING)
                                    ->color(fn(?RequestResponse $record) => match (true) {
                                        !$record => 'success',
                                        $record->created_at->diffInHours(now()) > 4 => 'danger',
                                        $record->created_at->diffInHours(now()) > 2 => 'warning',
                                        default => 'success',
                                    })
                                    ->formatStateUsing(fn(?RequestResponse $record) => match (true) {
                                        !$record => __('Waiting'),
                                        $record->created_at->diffInHours(now()) > 4 => __('Likely out of coverage'),
                                        $record->created_at->diffInHours(now()) > 2 => __('Delayed response'),
                                        default => __('Waiting'),
                                    }),

                                TextColumn::make('verification_date')
                                    ->label(__('Verification Date'))
                                    ->state(fn(RequestResponse $record) => $record->verified_at ? Carbon::parse($record->verified_at)->format('Y/m/d h:i A') : __('Not verified'))
                                    ->icon('heroicon-m-check-circle')
                                    ->color(fn(RequestResponse $record) => $record->verified_at ? 'success' : 'gray')
                                    ->size('xs'),
                            ])->space(1),
                        ]),
                ])
                    ->collapsible(),

            ])
                ->space(3),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->label(__('Response Status'))
                ->options(RequestResponseStatus::class)
                ->placeholder(__('All statuses'))
                ->native(false)
                ->multiple(),

            SelectFilter::make('blood_type')
                ->label(__('Blood Type'))
                ->options(BloodType::class)
                ->placeholder(__('All blood types'))
                ->native(false)
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['value'],
                        fn(Builder $query, $value): Builder => $query->whereHas('donor.healthProfile', function ($q) use ($value) {
                            $q->where('verified_blood_type', $value)
                                ->orWhere(function ($q2) use ($value) {
                                    $q2->whereNull('verified_blood_type')
                                        ->where('blood_type', $value);
                                });
                        })
                    );
                }),

            TernaryFilter::make('verified')
                ->label(__('Lab Verification'))
                ->placeholder(__('All'))
                ->trueLabel(__('Verified blood type'))
                ->falseLabel(__('Unverified blood type'))
                ->queries(
                    true: fn($query) => $query->whereHas('donor.healthProfile', fn($q) => $q->whereNotNull('verified_blood_type')),
                    false: fn($query) => $query->whereHas('donor.healthProfile', fn($q) => $q->whereNull('verified_blood_type')),
                ),

            Filter::make('created_at')
                ->label(__('Response Period'))
                ->form([
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('created_from')
                                ->label(__('From Date'))
                                ->native(false)
                                ->placeholder(__('Select Date')),
                            DatePicker::make('created_until')
                                ->label(__('To Date'))
                                ->native(false)
                                ->placeholder(__('Select Date')),
                        ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('mark_arrived')
                    ->label(__('Confirm Admission'))
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading(__('Confirm Donor Arrival'))
                    ->modalDescription(__('Has the donor arrived at the hospital and is ready for medical examination?'))
                    ->modalSubmitActionLabel(__('Yes, arrived'))
                    ->modalIcon('heroicon-o-check-circle')
                    ->visible(fn(RequestResponse $record) => $record->status === RequestResponseStatus::PENDING)
                    ->action(function (RequestResponse $record) {
                        $tenantId = filament()->getTenant()?->getKey();

                        if (! $tenantId) {
                            abort(403);
                        }

                        $record->loadMissing('bloodRequest');
                        $record->refresh();

                        if ((int) $record->bloodRequest?->organization_id !== (int) $tenantId) {
                            abort(403);
                        }

                        if ($record->status !== RequestResponseStatus::PENDING) {
                            $this->sendToast(
                                Notification::make()
                                    ->title(__('Invalid status'))
                                    ->body(__('This action is only allowed for pending responses.'))
                                    ->warning()
                            );
                            return;
                        }

                        $record->status = RequestResponseStatus::ACCEPTED;
                        $record->save();

                        $record->bloodRequest->organization->user->notify(
                            new \App\Notifications\DonorResponseNotification($record)
                        );

                        $this->sendToast(
                            Notification::make()
                                ->title(__('Donor arrival confirmed'))
                                ->success()
                        );
                    }),

                Action::make('mark_no_show')
                    ->label(__('Register No-Show'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('Register Donor No-Show'))
                    ->modalDescription(__('Do you want to register that the donor did not attend at the scheduled time?'))
                    ->modalSubmitActionLabel(__('Yes, register no-show'))
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->visible(fn(RequestResponse $record) => $record->status === RequestResponseStatus::PENDING)
                    ->action(function (RequestResponse $record) {
                        $tenantId = filament()->getTenant()?->getKey();

                        if (! $tenantId) {
                            abort(403);
                        }

                        $record->loadMissing('bloodRequest');
                        $record->refresh();

                        if ((int) $record->bloodRequest?->organization_id !== (int) $tenantId) {
                            abort(403);
                        }

                        if ($record->status !== RequestResponseStatus::PENDING) {
                            $this->sendToast(
                                Notification::make()
                                    ->title(__('Invalid status'))
                                    ->body(__('This action is only allowed for pending responses.'))
                                    ->warning()
                            );
                            return;
                        }

                        $record->status = RequestResponseStatus::NO_SHOW;
                        $record->save();

                        $record->bloodRequest->organization->user->notify(
                            new \App\Notifications\DonorResponseNotification($record)
                        );

                        $this->sendToast(
                            Notification::make()
                                ->title(__('No-show registered'))
                                ->body(__('Donor status updated to no-show'))
                                ->warning()
                        );
                    }),

                Action::make('medical_results')
                    ->label(__('Medical Assessment and Results'))
                    ->icon('heroicon-o-beaker')
                    ->color('info')
                    ->modalHeading(__('Medical Examination Results and Donation Status'))
                    ->modalDescription(__('Record lab test results and donation status for the donor. Their medical profile will be updated and they will be notified of the result.'))
                    ->modalSubmitActionLabel(__('Save results and inform donor'))
                    ->modalIcon('heroicon-o-beaker')
                    ->form([
                        Section::make(__('Blood Type Verification'))
                            ->description(__('Confirm the lab-verified blood type'))
                            ->compact()
                            ->schema([
                                Select::make('verified_blood_type')
                                    ->label(__('Lab-verified blood type'))
                                    ->options(BloodType::class)
                                    ->default(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ?? $record->donor->healthProfile?->blood_type)
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1)

                                    ->disabled(
                                        fn(RequestResponse $record) =>
                                        $record->donor->healthProfile?->verified_blood_type !== null &&
                                            $record->donor->healthProfile?->verified_by_organization_id !== null
                                    )
                                    ->dehydrated()
                                    ->helperText(
                                        fn(RequestResponse $record) =>
                                        $record->donor->healthProfile?->verified_blood_type
                                            ? __('Blood type is lab-verified and cannot be changed')
                                            : __('This blood type will be confirmed after verification')
                                    ),
                            ]),
                        Section::make(__('Medical Assessment and Eligibility'))
                            ->description(__('Determine donor eligibility based on examination'))
                            ->compact()
                            ->schema([
                                Select::make('eligibility_status')
                                    ->label(__('Donation Health Status'))
                                    ->options([
                                        'eligible' => __('Medically fit (donated successfully)'),
                                        'temporary' => __('Temporarily unfit (donation postponed)'),
                                        'permanent' => __('Permanently unfit (medical exclusion)'),
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Select::make('rejection_reason')
                                    ->label(__('Medical Exclusion Reason'))
                                    ->options(fn($get) => match ($get('eligibility_status')) {
                                        'temporary' => [
                                            'low_hemoglobin' => __('Low hemoglobin'),
                                            'underweight' => __('Underweight'),
                                            'recent_illness' => __('Recent illness / antibiotics'),
                                            'low_blood_pressure' => __('Low blood pressure'),
                                            'other_temp' => __('Other temporary medical reasons'),
                                        ],
                                        'permanent' => [
                                            'blood_virus' => __('Blood viruses (HCV/HBV/HIV)'),
                                            'chronic_disease' => __('Chronic disease preventing donation'),
                                            'heart_disease' => __('Heart diseases'),
                                            'cancer' => __('History of cancer'),
                                            'other_perm' => __('Other permanent medical reasons'),
                                        ],
                                        default => [],
                                    })
                                    ->required(fn($get) => in_array($get('eligibility_status'), ['temporary', 'permanent']))
                                    ->visible(fn($get) => in_array($get('eligibility_status'), ['temporary', 'permanent']))
                                    ->native(false),

                                Select::make('delay_duration')
                                    ->label(__('Temporary Exclusion Duration'))
                                    ->options([
                                        '1_week' => __('One week'),
                                        '2_weeks' => __('Two weeks'),
                                        '1_month' => __('One month'),
                                        '2_months' => __('Two months'),
                                        '3_months' => __('3 months'),
                                        '6_months' => __('6 months'),
                                        'custom' => __('Custom date...'),
                                    ])
                                    ->visible(fn($get) => $get('eligibility_status') === 'temporary')
                                    ->required(fn($get) => $get('eligibility_status') === 'temporary')
                                    ->default('3_months')
                                    ->native(false)
                                    ->live(),

                                DatePicker::make('custom_date')
                                    ->label(__('Specify custom date'))
                                    ->native(false)
                                    ->required(fn($get) => $get('delay_duration') === 'custom')
                                    ->visible(fn($get) => $get('delay_duration') === 'custom')
                                    ->minDate(now())
                                    ->live(),

                                Placeholder::make('next_date_preview')
                                    ->label(__('Expected Next Eligibility Date'))
                                    ->content(function ($get) {
                                        if (!$get('delay_duration')) {
                                            return '-';
                                        }

                                        return match ($get('delay_duration')) {
                                            '1_week' => now()->addWeek()->format('Y-m-d'),
                                            '2_weeks' => now()->addWeeks(2)->format('Y-m-d'),
                                            '1_month' => now()->addMonth()->format('Y-m-d'),
                                            '2_months' => now()->addMonths(2)->format('Y-m-d'),
                                            '3_months' => now()->addMonths(3)->format('Y-m-d'),
                                            '6_months' => now()->addMonths(6)->format('Y-m-d'),
                                            'custom' => $get('custom_date') ?? __('Please select a date'),
                                            default => now()->addMonths(3)->format('Y-m-d'),
                                        };
                                    })
                                    ->visible(fn($get) => $get('eligibility_status') === 'temporary')
                                    ->extraAttributes(['class' => 'text-primary-600 font-bold']),

                                Textarea::make('lab_notes')
                                    ->label(__('Medical Facility Notes'))
                                    ->placeholder(__('Internal notes about the case or results...'))
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->visible(fn(RequestResponse $record) => $record->status === RequestResponseStatus::ACCEPTED)
                    ->action(function (RequestResponse $record, array $data) {
                        $tenantId = filament()->getTenant()?->getKey();

                        if (! $tenantId) {
                            abort(403);
                        }

                        $record->loadMissing(['bloodRequest', 'donor.healthProfile']);
                        $record->refresh();

                        if ((int) $record->bloodRequest?->organization_id !== (int) $tenantId) {
                            abort(403);
                        }

                        if ($record->status !== RequestResponseStatus::ACCEPTED) {
                            $this->sendToast(
                                Notification::make()
                                    ->title(__('Invalid status'))
                                    ->body(__('This action is only allowed for accepted responses.'))
                                    ->warning()
                            );
                            return;
                        }

                        $healthProfile = $record->donor->healthProfile;
                        $orgId = (int) $tenantId;

                        DB::transaction(function () use ($record, $healthProfile, $data, $orgId) {

                            $record->verified_at = Carbon::now();
                            $wasCompleted = $record->status === RequestResponseStatus::COMPLETED;

                            if ($record->status === RequestResponseStatus::ACCEPTED) {
                                if ($data['eligibility_status'] === 'eligible') {
                                    $record->status = RequestResponseStatus::COMPLETED;
                                } else {
                                    $record->status = RequestResponseStatus::DECLINED;
                                }
                            }

                            $record->save();

                            if (!$wasCompleted && $record->status === RequestResponseStatus::COMPLETED) {
                                $request = $record->bloodRequest;
                                $completedCount = $request->responses()
                                    ->where('status', RequestResponseStatus::COMPLETED)
                                    ->count();

                                if ($completedCount >= $request->units_needed) {
                                    $request->status = BloodRequestStatus::FULFILLED;
                                    $request->fulfilled_at = now();
                                    $request->save();

                                    \App\Jobs\CancelExcessResponsesJob::dispatchSync($request);
                                }
                            }

                            if ($healthProfile) {

                                if (empty($healthProfile->verified_by_organization_id) || $healthProfile->verified_by_organization_id === $orgId) {
                                    $healthProfile->verified_blood_type = $data['verified_blood_type'];
                                    $healthProfile->verified_by_organization_id = $orgId;
                                    $healthProfile->verified_at = Carbon::now();
                                }

                                if ($data['eligibility_status'] === 'permanent') {
                                    $healthProfile->chronic_disease = true;
                                    $healthProfile->is_eligible = false;
                                } elseif ($data['eligibility_status'] === 'temporary') {
                                    $healthProfile->is_eligible = false;

                                    $nextDate = Carbon::now();
                                    $nextDate = match ($data['delay_duration']) {
                                        '1_week' => $nextDate->addWeek(),
                                        '2_weeks' => $nextDate->addWeeks(2),
                                        '1_month' => $nextDate->addMonth(),
                                        '2_months' => $nextDate->addMonths(2),
                                        '3_months' => $nextDate->addMonths(3),
                                        '6_months' => $nextDate->addMonths(6),
                                        'custom' => Carbon::parse($data['custom_date']),
                                        default => $nextDate->addMonths(3),
                                    };

                                    $healthProfile->next_eligible_date = $nextDate;
                                } else {

                                    $healthProfile->is_eligible = true;
                                    $healthProfile->next_eligible_date = null;

                                    $healthProfile->recent_donation = true;
                                    $healthProfile->last_donation_date = Carbon::now();
                                    $healthProfile->total_donations = ($healthProfile->total_donations ?? 0) + 1;
                                }

                                $healthProfile->save();
                            }

                            EligibilityLog::create([
                                'donor_id' => $record->donor_id,
                                'organization_id' => $orgId,
                                'check_type' => EligibilityLog::TYPE_LAB_VERIFICATION,
                                'is_eligible' => $data['eligibility_status'] === 'eligible',
                                'is_permanent' => $data['eligibility_status'] === 'permanent',
                                'rejection_reason' => $data['rejection_reason'] ?? null,
                                'answers_snapshot' => [
                                    'lab_notes' => $data['lab_notes'],
                                    'blood_type_at_check' => $data['verified_blood_type'],
                                ]
                            ]);
                        });

                        $orgUser = $record->bloodRequest->organization?->user;
                        if ($orgUser) {
                            $record->load(['donor.user', 'donor.healthProfile', 'bloodRequest.organization']);
                            $orgUser->notify(new \App\Notifications\DonorResponseNotification($record));
                        }

                        if (in_array($data['eligibility_status'], ['temporary', 'permanent'])) {
                            $record->donor->user->notify(
                                new \App\Notifications\DonorIneligibilityNotification(
                                    eligibilityStatus: $data['eligibility_status'],
                                    rejectionReason: $data['rejection_reason'] ?? null,
                                    nextEligibleDate: $healthProfile?->next_eligible_date,
                                    organizationName: $record->bloodRequest->organization?->getTranslation('org_name', app()->getLocale()),
                                )
                            );
                        }

                        $this->sendToast(
                            Notification::make()
                                ->title(__('Results saved successfully'))
                                ->success()
                        );
                    }),

                ViewAction::make()
                    ->label(__('View Details'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(__('Donor Response Details'))
                    ->modalIcon('heroicon-o-user-circle')
                    ->form([

                        Section::make(__('Donor'))
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->schema([
                                Placeholder::make('donor_name')
                                    ->label(__('Name'))
                                    ->content(fn($record) => $record->donor->user->name ?? '—'),

                                Placeholder::make('donor_phone')
                                    ->label(__('Phone Number'))
                                    ->content(fn($record) => $record->donor->user->phone ?? '—'),

                                Placeholder::make('donor_status')
                                    ->label(__('Response Status'))
                                    ->content(fn($record) => $record->status->getLabel()),

                                Placeholder::make('responded_at')
                                    ->label(__('Approval Date'))
                                    ->content(fn($record) => $record->responded_at?->format('Y-m-d H:i') ?? '—'),
                            ]),

                        Section::make(__('Blood Type'))
                            ->icon('heroicon-o-beaker')
                            ->columns(2)
                            ->schema([
                                Placeholder::make('self_reported_blood_type')
                                    ->label(__('Self-declared blood type'))
                                    ->content(fn($record) => $record->donor->healthProfile?->blood_type?->getLabel() ?? '—'),

                                Placeholder::make('verified_blood_type_view')
                                    ->label(__('Lab-verified blood type'))
                                    ->content(fn($record) => $record->donor->healthProfile?->verified_blood_type?->getLabel() ?? __('Not yet verified')),

                                Placeholder::make('verifying_org_view')
                                    ->label(__('Verifying Entity'))
                                    ->content(fn($record) => $record->donor->healthProfile?->verifyingOrganization?->org_name ?? '—'),

                                Placeholder::make('verified_at_view')
                                    ->label(__('Lab Verification Date'))
                                    ->content(fn($record) => $record->donor->healthProfile?->verified_at?->format('Y-m-d H:i') ?? '—'),
                            ]),

                        Section::make(__('Health Eligibility'))
                            ->icon('heroicon-o-heart')
                            ->columns(2)
                            ->schema([
                                Placeholder::make('eligibility_view')
                                    ->label(__('Eligibility Status'))
                                    ->content(fn($record) => $record->donor->healthProfile?->is_eligible
                                        ? __('Eligible for donation')
                                        : __('Currently ineligible')),

                                Placeholder::make('next_eligible_date')
                                    ->label(__('Next Eligibility Date'))
                                    ->content(fn($record) => $record->donor->healthProfile?->next_eligible_date?->format('Y-m-d') ?? '—'),

                                Placeholder::make('total_donations')
                                    ->label(__('Total Donations'))
                                    ->content(fn($record) => ($record->donor->healthProfile?->total_donations ?? 0) . ' ' . __('Donation')),

                                Placeholder::make('last_donation')
                                    ->label(__('Last Donation Date'))
                                    ->content(fn($record) => $record->donor->healthProfile?->last_donation_date?->format('Y-m-d') ?? '—'),
                            ]),

                        Section::make(__('QR Code'))
                            ->icon('heroicon-o-qr-code')
                            ->collapsed()
                            ->columns(2)
                            ->schema([
                                Placeholder::make('qr_state')
                                    ->label(__('Code Status'))
                                    ->content(fn($record) => $record->qr_state_label),

                                Placeholder::make('qr_expires_at')
                                    ->label(__('Expires at'))
                                    ->content(fn($record) => $record->qr_code_expires_at?->format('Y-m-d H:i') ?? '—'),

                                Placeholder::make('verified_via_qr_at')
                                    ->label(__('Scan Date'))
                                    ->content(fn($record) => $record->verified_at?->format('Y-m-d H:i') ?? __('Not yet scanned')),
                            ]),
                    ]),
            ])
                ->label(__('Actions'))
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('primary')
                ->button(),
        ];
    }
}
