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
use Filament\Support\Enums\FontWeight;
use App\Enums\NotificationType;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use App\Models\EligibilityLog;
use Filament\Actions\ActionGroup;
use Filament\Tables\Enums\FiltersLayout;
use App\Enums\BloodRequestStatus;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'Donor Responses';

    protected static string|BackedEnum|null $icon = 'heroicon-o-users';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('organization.update_response_status'))
                    ->description(__('organization.choose_the_appropriate_status_for_the_response'))
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Select::make('status')
                            ->label(__('organization.status'))
                            ->options(RequestResponseStatus::class)
                            ->required()
                            ->native(false)
                            ->placeholder(__('organization.choose_status'))
                            ->helperText(__('organization.the_donor_response_status_will_be_updated')),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading(__('organization.responding_donors_list'))
            ->description(__('organization.view_and_manage_donor_responses_for_this_request'))
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
            ->emptyStateHeading(__('organization.no_responses_yet'))
            ->emptyStateDescription(__('organization.no_donors_have_responded_to_this_request_yet'))
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
                            ->label(__('organization.donor_name'))
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->searchable()
                            ->sortable()
                            ->icon('heroicon-m-user')
                            ->iconColor('primary'),

                        TextColumn::make('donor.user.phone')
                            ->label(__('organization.phone_number'))
                            ->icon('heroicon-m-phone')
                            ->color('gray')
                            ->size('sm')
                            ->default(__('organization.none')),
                    ])->space(1),

                    TextColumn::make('status')
                        ->label(__('organization.status'))
                        ->badge()
                        ->size('md')
                        ->description(fn(RequestResponse $record) =>
                        !$record->donor->healthProfile?->is_eligible
                            ? __('organization.currently_ineligible_for_donation')
                            : null)
                        ->grow(false),
                ]),

                Panel::make([
                    TableGrid::make(2)
                        ->schema([
                            Stack::make([
                                TextColumn::make('blood_type_header')
                                    ->label(__('organization.blood_type'))
                                    ->state(__('organization.blood_type'))
                                    ->size('xs')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextColumn::make('blood_type_display')
                                    ->state(
                                        fn(RequestResponse $record) =>
                                        $record->donor->healthProfile?->verified_blood_type?->getLabel() ??
                                            $record->donor->healthProfile?->blood_type?->getLabel() ??
                                            __('organization.not_specified')
                                    )
                                    ->badge()
                                    ->size('lg')
                                    ->icon(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? 'heroicon-m-check-badge' : 'heroicon-m-question-mark-circle')
                                    ->color(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? 'success' : 'warning'),

                                TextColumn::make('blood_verification')
                                    ->state(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? __('organization.lab_verified') : __('organization.self_reported'))
                                    ->size('xs')
                                    ->color(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? 'success' : 'warning')
                                    ->weight(FontWeight::SemiBold),
                            ])->space(1),

                            Stack::make([
                                TextColumn::make('timing_header')
                                    ->state(__('organization.timing'))
                                    ->size('xs')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextColumn::make('response_date')
                                    ->label(__('organization.response_date'))
                                    ->state(fn(RequestResponse $record) => $record->created_at->diffForHumans())
                                    ->icon('heroicon-m-clock')
                                    ->color('info')
                                    ->size('sm')
                                    ->weight(FontWeight::Medium),

                                TextColumn::make('connectivity_status')
                                    ->label(__('organization.connectivity_status'))
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
                                        !$record => __('organization.waiting'),
                                        $record->created_at->diffInHours(now()) > 4 => __('organization.likely_out_of_coverage'),
                                        $record->created_at->diffInHours(now()) > 2 => __('organization.delayed_response'),
                                        default => __('organization.waiting'),
                                    }),

                                TextColumn::make('verification_date')
                                    ->label(__('organization.verification_date'))
                                    ->state(fn(RequestResponse $record) => $record->verified_at ? Carbon::parse($record->verified_at)->format('Y/m/d h:i A') : __('admin.not_verified'))
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
                ->label(__('organization.response_status'))
                ->options(RequestResponseStatus::class)
                ->placeholder(__('organization.all_statuses'))
                ->native(false)
                ->multiple(),

            SelectFilter::make('blood_type')
                ->label(__('organization.blood_type'))
                ->options(BloodType::class)
                ->placeholder(__('organization.all_blood_types'))
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
                ->label(__('organization.lab_verification'))
                ->placeholder(__('admin.all'))
                ->trueLabel(__('organization.verified_blood_type'))
                ->falseLabel(__('organization.unverified_blood_type'))
                ->queries(
                    true: fn($query) => $query->whereHas('donor.healthProfile', fn($q) => $q->whereNotNull('verified_blood_type')),
                    false: fn($query) => $query->whereHas('donor.healthProfile', fn($q) => $q->whereNull('verified_blood_type')),
                ),

            Filter::make('created_at')
                ->label(__('organization.response_period'))
                ->form([
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('created_from')
                                ->label(__('organization.from_date'))
                                ->native(false)
                                ->placeholder(__('organization.select_date')),
                            DatePicker::make('created_until')
                                ->label(__('organization.to_date'))
                                ->native(false)
                                ->placeholder(__('organization.select_date')),
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
                    ->label(__('organization.confirm_admission'))
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading(__('organization.confirm_donor_arrival'))
                    ->modalDescription(__('organization.has_the_donor_arrived_at_the_hospital'))
                    ->modalSubmitActionLabel(__('organization.yes_arrived'))
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
                            return;
                        }

                        $record->status = RequestResponseStatus::ACCEPTED;
                        $record->verified_at = now();
                        $record->save();

                        $orgUser = $record->bloodRequest->organization?->user;
                        if ($orgUser) {
                            $record->load(['donor.user', 'donor.healthProfile', 'bloodRequest.organization']);
                            app(NotificationService::class)->send(
                                $orgUser,
                                new \App\Notifications\DonorResponseNotification($record),
                                NotificationType::DONOR_RESPONSE
                            );
                        }
                    }),

                Action::make('mark_no_show')
                    ->label(__('organization.register_no_show'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('organization.register_donor_no_show'))
                    ->modalDescription(__('organization.do_you_want_to_register_that_the_donor_did_not_attend'))
                    ->modalSubmitActionLabel(__('organization.yes_register_no_show'))
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
                            return;
                        }

                        $record->status = RequestResponseStatus::NO_SHOW;
                        $record->save();

                        $orgUser = $record->bloodRequest->organization?->user;
                        if ($orgUser) {
                            $record->load(['donor.user', 'donor.healthProfile', 'bloodRequest.organization']);
                            app(NotificationService::class)->send(
                                $orgUser,
                                new \App\Notifications\DonorResponseNotification($record),
                                NotificationType::DONOR_RESPONSE
                            );
                        }
                    }),

                Action::make('medical_results')
                    ->label(__('organization.medical_assessment_and_results'))
                    ->icon('heroicon-o-beaker')
                    ->color('info')
                    ->modalHeading(__('organization.medical_examination_results_and_donation_status'))
                    ->modalDescription(__('organization.record_lab_test_results_and_donation_status'))
                    ->modalSubmitActionLabel(__('organization.save_results_and_inform_donor'))
                    ->modalIcon('heroicon-o-beaker')
                    ->form([
                        Section::make(__('organization.blood_type_verification'))
                            ->description(__('organization.confirm_lab_verified_blood_type'))
                            ->compact()
                            ->schema([
                                Select::make('verified_blood_type')
                                    ->label(__('organization.lab_verified_blood_type'))
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
                                            ? __('organization.blood_type_is_lab_verified_and_cannot_be_changed')
                                            : __('organization.this_blood_type_will_be_confirmed_after_verification')
                                    ),
                            ]),
                        Section::make(__('organization.medical_assessment_and_eligibility'))
                            ->description(__('organization.determine_donor_eligibility_based_on_examination'))
                            ->compact()
                            ->schema([
                                Select::make('eligibility_status')
                                    ->label(__('organization.donation_health_status'))
                                    ->options([
                                        'eligible' => __('organization.medically_fit_donated_successfully'),
                                        'temporary' => __('organization.temporarily_unfit_donation_postponed'),
                                        'permanent' => __('organization.permanently_unfit_medical_exclusion'),
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Select::make('rejection_reason')
                                    ->label(__('organization.medical_exclusion_reason'))
                                    ->options(fn($get) => match ($get('eligibility_status')) {
                                        'temporary' => [
                                            'low_hemoglobin' => __('organization.low_hemoglobin'),
                                            'underweight' => __('organization.underweight'),
                                            'recent_illness' => __('organization.recent_illness_antibiotics'),
                                            'low_blood_pressure' => __('organization.low_blood_pressure'),
                                            'other_temp' => __('organization.other_temporary_medical_reasons'),
                                        ],
                                        'permanent' => [
                                            'blood_virus' => __('organization.blood_viruses'),
                                            'chronic_disease' => __('organization.chronic_disease_preventing_donation'),
                                            'heart_disease' => __('organization.heart_diseases'),
                                            'cancer' => __('organization.history_of_cancer'),
                                            'other_perm' => __('organization.other_permanent_medical_reasons'),
                                        ],
                                        default => [],
                                    })
                                    ->required(fn($get) => in_array($get('eligibility_status'), ['temporary', 'permanent']))
                                    ->visible(fn($get) => in_array($get('eligibility_status'), ['temporary', 'permanent']))
                                    ->native(false),

                                Select::make('delay_duration')
                                    ->label(__('organization.temporary_exclusion_duration'))
                                    ->options([
                                        '1_week' => __('organization.one_week'),
                                        '2_weeks' => __('organization.two_weeks'),
                                        '1_month' => __('organization.one_month'),
                                        '2_months' => __('organization.two_months'),
                                        '3_months' => __('organization.three_months'),
                                        '6_months' => __('organization.six_months'),
                                        'custom' => __('organization.custom_date_option'),
                                    ])
                                    ->visible(fn($get) => $get('eligibility_status') === 'temporary')
                                    ->required(fn($get) => $get('eligibility_status') === 'temporary')
                                    ->default('3_months')
                                    ->native(false)
                                    ->live(),

                                DatePicker::make('custom_date')
                                    ->label(__('organization.specify_custom_date'))
                                    ->native(false)
                                    ->required(fn($get) => $get('delay_duration') === 'custom')
                                    ->visible(fn($get) => $get('delay_duration') === 'custom')
                                    ->minDate(now())
                                    ->live(),

                                Placeholder::make('next_date_preview')
                                    ->label(__('organization.expected_next_eligibility_date'))
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
                                            'custom' => $get('custom_date') ?? __('organization.please_select_a_date'),
                                            default => now()->addMonths(3)->format('Y-m-d'),
                                        };
                                    })
                                    ->visible(fn($get) => $get('eligibility_status') === 'temporary')
                                    ->extraAttributes(['class' => 'text-primary-600 font-bold']),

                                Textarea::make('lab_notes')
                                    ->label(__('organization.medical_facility_notes'))
                                    ->placeholder(__('organization.internal_notes_about_the_case_or_results'))
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
                            return;
                        }

                        $healthProfile = $record->donor->healthProfile;
                        $orgId = (int) $tenantId;

                        DB::transaction(function () use ($record, $healthProfile, $data, $orgId) {

                            $record->verified_at = Carbon::now();

                            if ($data['eligibility_status'] === 'eligible') {
                                $record->status = RequestResponseStatus::COMPLETED;
                            } else {
                                $record->status = RequestResponseStatus::DECLINED;
                            }

                            $record->save();

                            if ($record->status === RequestResponseStatus::COMPLETED) {
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
                            app(NotificationService::class)->send(
                                $orgUser,
                                new \App\Notifications\DonorResponseNotification($record),
                                NotificationType::DONOR_RESPONSE
                            );
                        }

                        if (in_array($data['eligibility_status'], ['temporary', 'permanent'])) {
                            $donorUser = $record->donor->user;
                            if ($donorUser) {
                                app(NotificationService::class)->send(
                                    $donorUser,
                                    new \App\Notifications\DonorIneligibilityNotification(
                                        eligibilityStatus: $data['eligibility_status'],
                                        rejectionReason: $data['rejection_reason'] ?? null,
                                        nextEligibleDate: $healthProfile?->next_eligible_date,
                                        organizationName: $record->bloodRequest->organization?->localized_org_name,
                                    ),
                                    NotificationType::DONOR_INELIGIBILITY
                                );
                            }
                        }

                    }),

                ViewAction::make()
                    ->label(__('organization.view_details'))
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(__('organization.donor_response_details'))
                    ->modalIcon('heroicon-o-user-circle')
                    ->form([

                        Section::make(__('organization.donor'))
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->schema([
                                Placeholder::make('donor_name')
                                    ->label(__('organization.name'))
                                    ->content(fn($record) => $record->donor->user->name ?? '—'),

                                Placeholder::make('donor_phone')
                                    ->label(__('organization.phone_number'))
                                    ->content(fn($record) => $record->donor->user->phone ?? '—'),

                                Placeholder::make('donor_status')
                                    ->label(__('organization.response_status'))
                                    ->content(fn($record) => $record->status->getLabel()),

                                Placeholder::make('responded_at')
                                    ->label(__('organization.approval_date'))
                                    ->content(fn($record) => $record->responded_at?->format('Y-m-d H:i') ?? '—'),
                            ]),

                        Section::make(__('organization.blood_type'))
                            ->icon('heroicon-o-beaker')
                            ->columns(2)
                            ->schema([
                                Placeholder::make('self_reported_blood_type')
                                    ->label(__('organization.self_declared_blood_type'))
                                    ->content(fn($record) => $record->donor->healthProfile?->blood_type?->getLabel() ?? '—'),

                                Placeholder::make('verified_blood_type_view')
                                    ->label(__('organization.lab_verified_blood_type'))
                                    ->content(fn($record) => $record->donor->healthProfile?->verified_blood_type?->getLabel() ?? __('organization.not_yet_verified')),

                                Placeholder::make('verifying_org_view')
                                    ->label(__('organization.verifying_entity'))
                                    ->content(fn($record) => $record->donor->healthProfile?->verifyingOrganization?->localized_org_name ?? '—'),

                                Placeholder::make('verified_at_view')
                                    ->label(__('organization.lab_verification_date'))
                                    ->content(fn($record) => $record->donor->healthProfile?->verified_at?->format('Y-m-d H:i') ?? '—'),
                            ]),

                        Section::make(__('organization.health_eligibility'))
                            ->icon('heroicon-o-heart')
                            ->columns(2)
                            ->schema([
                                Placeholder::make('eligibility_view')
                                    ->label(__('organization.eligibility_status'))
                                    ->content(fn($record) => $record->donor->healthProfile?->is_eligible
                                        ? __('organization.eligible_for_donation')
                                        : __('organization.currently_ineligible')),

                                Placeholder::make('next_eligible_date')
                                    ->label(__('organization.next_eligibility_date'))
                                    ->content(fn($record) => $record->donor->healthProfile?->next_eligible_date?->format('Y-m-d') ?? '—'),

                                Placeholder::make('total_donations')
                                    ->label(__('organization.total_donations'))
                                    ->content(fn($record) => ($record->donor->healthProfile?->total_donations ?? 0) . ' ' . __('organization.donation')),

                                Placeholder::make('last_donation')
                                    ->label(__('organization.last_donation_date'))
                                    ->content(fn($record) => $record->donor->healthProfile?->last_donation_date?->format('Y-m-d') ?? '—'),
                            ]),

                        Section::make(__('organization.qr_code'))
                            ->icon('heroicon-o-qr-code')
                            ->collapsed()
                            ->columns(2)
                            ->schema([
                                Placeholder::make('qr_state')
                                    ->label(__('organization.code_status'))
                                    ->content(fn($record) => $record->qr_state_label),

                                Placeholder::make('qr_expires_at')
                                    ->label(__('organization.expires_at'))
                                    ->content(fn($record) => $record->qr_code_expires_at?->format('Y-m-d H:i') ?? '—'),

                                Placeholder::make('verified_via_qr_at')
                                    ->label(__('organization.scan_date'))
                                    ->content(fn($record) => $record->verified_at?->format('Y-m-d H:i') ?? __('organization.not_yet_scanned')),
                            ]),
                    ]),
            ])
                ->label(__('admin.options'))
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('primary')
                ->button(),
        ];
    }
}
