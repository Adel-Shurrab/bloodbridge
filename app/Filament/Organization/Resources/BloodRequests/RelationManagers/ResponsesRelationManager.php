<?php

namespace App\Filament\Organization\Resources\BloodRequests\RelationManagers;

use App\Models\RequestResponse;
use BackedEnum;
use App\Models\BloodRequest;
use App\Enums\BloodType;
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

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'استجابات المتبرعين';

    protected static string|BackedEnum|null $icon = 'heroicon-o-users';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('تحديث حالة الاستجابة')
                    ->description('اختر الحالة المناسبة للاستجابة')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Select::make('status')
                            ->label('الحالة')
                            ->options(\App\Enums\RequestResponseStatus::class)
                            ->required()
                            ->native(false)
                            ->placeholder('اختر الحالة')
                            ->helperText('سيتم تحديث حالة استجابة المتبرع'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->heading('قائمة المتبرعين المستجيبين')
            ->description('عرض وإدارة استجابات المتبرعين لهذا الطلب')
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
            ->emptyStateHeading('لا توجد استجابات بعد')
            ->emptyStateDescription('لم يستجب أي متبرع لهذا الطلب حتى الآن')
            ->emptyStateIcon('heroicon-o-users')
            ->paginated([12, 24, 48, 'all'])
            ->defaultPaginationPageOption(12)
            ->defaultSort('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Stack::make([
                // Header: Name & Status Badge
                Split::make([
                    Stack::make([
                        TextColumn::make('donor.user.name')
                            ->label('اسم المتبرع')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->searchable()
                            ->sortable()
                            ->icon('heroicon-m-user')
                            ->iconColor('primary'),

                        TextColumn::make('donor.user.phone')
                            ->label('رقم الهاتف')
                            ->icon('heroicon-m-phone')
                            ->color('gray')
                            ->size('sm')
                            ->default('لا يوجد'),
                    ])->space(1),

                    TextColumn::make('status')
                        ->label('الحالة')
                        ->badge()
                        ->size('md')
                        ->description(fn(RequestResponse $record) =>
                        !$record->donor->healthProfile?->is_eligible
                            ? '🚫 غير مؤهل للتبرع حالياً'
                            : null)
                        ->grow(false),
                ]),

                // Panel: Blood Type & Timing Info
                Panel::make([
                    TableGrid::make(2)
                        ->schema([
                            Stack::make([
                                TextColumn::make('blood_type_header')
                                    ->label('الفصيلة')
                                    ->state('فصيلة الدم')
                                    ->size('xs')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextColumn::make('blood_type_display')
                                    ->state(
                                        fn(RequestResponse $record) =>
                                        $record->donor->healthProfile?->verified_blood_type?->getLabel() ??
                                            $record->donor->healthProfile?->blood_type?->getLabel() ??
                                            'غير محدد'
                                    )
                                    ->badge()
                                    ->size('lg')
                                    ->icon(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? 'heroicon-m-check-badge' : 'heroicon-m-question-mark-circle')
                                    ->color(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? 'success' : 'warning'),

                                TextColumn::make('blood_verification')
                                    ->state(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? '✓ محققة مخبرياً' : '⚠ مصرح بها ذاتياً')
                                    ->size('xs')
                                    ->color(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ? 'success' : 'warning')
                                    ->weight(FontWeight::SemiBold),
                            ])->space(1),

                            Stack::make([
                                TextColumn::make('timing_header')
                                    ->state('التوقيت')
                                    ->size('xs')
                                    ->color('gray')
                                    ->weight(FontWeight::Medium),

                                TextColumn::make('response_date')
                                    ->label('تاريخ الاستجابة')
                                    ->state(fn(RequestResponse $record) => $record->created_at->diffForHumans())
                                    ->icon('heroicon-m-clock')
                                    ->color('info')
                                    ->size('sm')
                                    ->weight(FontWeight::Medium),

                                TextColumn::make('verification_date')
                                    ->label('تاريخ التحقق')
                                    ->state(fn(RequestResponse $record) => $record->verified_at ? Carbon::parse($record->verified_at)->format('Y/m/d h:i A') : 'لم يتم التحقق')
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
                ->label('حالة الاستجابة')
                ->options(\App\Enums\RequestResponseStatus::class)
                ->placeholder('جميع الحالات')
                ->native(false)
                ->multiple(),

            SelectFilter::make('blood_type')
                ->label('فصيلة الدم')
                ->options(BloodType::class)
                ->placeholder('جميع الفصائل')
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
                ->label('التحقق المخبري')
                ->placeholder('الكل')
                ->trueLabel('فصيلة محققة')
                ->falseLabel('فصيلة غير محققة')
                ->queries(
                    true: fn($query) => $query->whereHas('donor.healthProfile', fn($q) => $q->whereNotNull('verified_blood_type')),
                    false: fn($query) => $query->whereHas('donor.healthProfile', fn($q) => $q->whereNull('verified_blood_type')),
                ),

            Filter::make('created_at')
                ->label('فترة الاستجابة')
                ->form([
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('created_from')
                                ->label('من تاريخ')
                                ->native(false)
                                ->placeholder('اختر التاريخ'),
                            DatePicker::make('created_until')
                                ->label('إلى تاريخ')
                                ->native(false)
                                ->placeholder('اختر التاريخ'),
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
                    ->label('تأكيد الحضور')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد وصول المتبرع')
                    ->modalDescription('هل وصل المتبرع إلى المستشفى وجاهز للفحص الطبي؟')
                    ->modalSubmitActionLabel('نعم، وصل')
                    ->modalIcon('heroicon-o-check-circle')
                    ->visible(fn(RequestResponse $record) => $record->status === \App\Enums\RequestResponseStatus::PENDING)
                    ->action(function (RequestResponse $record) {
                        $record->status = \App\Enums\RequestResponseStatus::PENDING;
                        $record->save();

                        // Notify organization
                        $record->bloodRequest->organization->user->notify(
                            new \App\Notifications\DonorResponseNotification($record)
                        );

                        Notification::make()
                            ->title('تم تأكيد موافقة المتبرع')
                            ->success()
                            ->send();
                    }),

                Action::make('verify_donation')
                    ->label('تأكيد التبرع')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد اكتمال التبرع')
                    ->modalDescription('هل تريد تأكيد أن المتبرع قد أكمل عملية التبرع بنجاح؟')
                    ->modalSubmitActionLabel('نعم، تأكيد التبرع')
                    ->modalIcon('heroicon-o-check-badge')
                    ->visible(fn(RequestResponse $record) => in_array($record->status, [\App\Enums\RequestResponseStatus::PENDING, \App\Enums\RequestResponseStatus::ACCEPTED]))
                    ->action(function (RequestResponse $record) {
                        DB::transaction(function () use ($record) {
                            $record->status = \App\Enums\RequestResponseStatus::COMPLETED;
                            $record->verified_at = now();
                            $record->save();

                            // Update donor health profile - CRITICAL for 90-day cooldown
                            $healthProfile = $record->donor->healthProfile;
                            if ($healthProfile) {
                                $healthProfile->recent_donation = true; // Must set this or booted() will erase last_donation_date!
                                $healthProfile->last_donation_date = now();
                                $healthProfile->total_donations = ($healthProfile->total_donations ?? 0) + 1;
                                $healthProfile->save();
                            }

                            // Increment counters in BloodRequest
                            $request = $record->bloodRequest;
                            $request->increment('donors_completed', 1);

                            // Notify organization
                            $request->organization->user->notify(
                                new \App\Notifications\DonorResponseNotification($record)
                            );

                            // Update Request status to FULFILLED if units reached
                            if ($request->donors_completed >= $request->units_needed) {
                                $request->status = \App\Enums\BloodRequestStatus::FULFILLED;
                                $request->fulfilled_at = now();
                                $request->save();
                            }
                        });

                        Notification::make()
                            ->title('تم تأكيد التبرع بنجاح')
                            ->body('تم تسجيل اكتمال عملية التبرع وتحديث العدادات')
                            ->success()
                            ->send();
                    }),

                Action::make('mark_no_show')
                    ->label('تسجيل عدم حضور')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('تسجيل عدم حضور المتبرع')
                    ->modalDescription('هل تريد تسجيل أن المتبرع لم يحضر في الموعد المحدد؟')
                    ->modalSubmitActionLabel('نعم، تسجيل عدم الحضور')
                    ->modalIcon('heroicon-o-exclamation-triangle')
                    ->visible(fn(RequestResponse $record) => $record->status === \App\Enums\RequestResponseStatus::PENDING)
                    ->action(function (RequestResponse $record) {
                        $record->status = \App\Enums\RequestResponseStatus::NO_SHOW;
                        $record->save();

                        // Notify organization
                        $record->bloodRequest->organization->user->notify(
                            new \App\Notifications\DonorResponseNotification($record)
                        );

                        Notification::make()
                            ->title('تم تسجيل عدم الحضور')
                            ->body('تم تحديث حالة المتبرع إلى لم يحضر')
                            ->warning()
                            ->send();
                    }),

                Action::make('medical_results')
                    ->label('التقييم والنتائج الطبية')
                    ->icon('heroicon-o-beaker')
                    ->color('info')
                    ->modalHeading('نتائج الفحص الطبي وحالة التبرع')
                    ->modalDescription('سجل نتائج الفحص المخبري وحالة التبرع للمتبرع. سيتم تحديث ملفه الطبي وإشعاره بالنتيجة.')
                    ->modalSubmitActionLabel('حفظ النتائج وإبلاغ المتبرع')
                    ->modalIcon('heroicon-o-beaker')
                    ->form([
                        Section::make('التحقق من الفصيلة')
                            ->description('تثبيت فصيلة الدم المخبرية المؤكدة')
                            ->compact()
                            ->schema([
                                Select::make('verified_blood_type')
                                    ->label('الفصيلة المؤكدة مخبرياً')
                                    ->options(BloodType::class)
                                    ->default(fn(RequestResponse $record) => $record->donor->healthProfile?->verified_blood_type ?? $record->donor->healthProfile?->blood_type)
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1)
                                    // Lock field if already verified by another organization
                                    ->disabled(
                                        fn(RequestResponse $record) =>
                                        $record->donor->healthProfile?->verified_blood_type !== null &&
                                            $record->donor->healthProfile?->verified_by_organization_id !== null
                                    )
                                    ->helperText(
                                        fn(RequestResponse $record) =>
                                        $record->donor->healthProfile?->verified_blood_type
                                            ? '🔒 الفصيلة محققة مخبرياً ولا يمكن تغييرها'
                                            : 'سيتم تثبيت هذه الفصيلة بعد التحقق'
                                    ),
                            ]),
                        Section::make('التقييم الطبي والأهلية')
                            ->description('تحديد مدى أهلية المتبرع بناءً على الفحص')
                            ->compact()
                            ->schema([
                                Select::make('eligibility_status')
                                    ->label('الحالة الصحية للتبرع')
                                    ->options([
                                        'eligible' => 'لائق طبيًا (تم التبرع بنجاح)',
                                        'temporary' => 'غير لائق مؤقتًا (تأجيل التبرع)',
                                        'permanent' => 'غير لائق دائمًا (استبعاد طبي)',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Select::make('rejection_reason')
                                    ->label('سبب الاستبعاد الطبي')
                                    ->options(fn($get) => match ($get('eligibility_status')) {
                                        'temporary' => [
                                            'low_hemoglobin' => 'نقص الهيموجلوبين',
                                            'underweight' => 'نقص الوزن',
                                            'recent_illness' => 'مرض حديث / مضادات حيوية',
                                            'low_blood_pressure' => 'انخفاض ضغط الدم',
                                            'other_temp' => 'أسباب طبية مؤقتة أخرى',
                                        ],
                                        'permanent' => [
                                            'blood_virus' => 'وجود فيروسات في الدم (HCV/HBV/HIV)',
                                            'chronic_disease' => 'مرض مزمن يمنع التبرع',
                                            'heart_disease' => 'أمراض القلب',
                                            'cancer' => 'تاريخ مرضي للسرطان',
                                            'other_perm' => 'أسباب طبية دائمة أخرى',
                                        ],
                                        default => [],
                                    })
                                    ->required(fn($get) => in_array($get('eligibility_status'), ['temporary', 'permanent']))
                                    ->visible(fn($get) => in_array($get('eligibility_status'), ['temporary', 'permanent']))
                                    ->native(false),

                                Select::make('delay_duration')
                                    ->label('مدة الاستبعاد المؤقت')
                                    ->options([
                                        '1_week' => 'أسبوع واحد',
                                        '2_weeks' => 'أسبوعين',
                                        '1_month' => 'شهر واحد',
                                        '2_months' => 'شهرين',
                                        '3_months' => '3 أشهر',
                                        '6_months' => '6 أشهر',
                                        'custom' => 'تاريخ مخصص...',
                                    ])
                                    ->visible(fn($get) => $get('eligibility_status') === 'temporary')
                                    ->required(fn($get) => $get('eligibility_status') === 'temporary')
                                    ->default('3_months')
                                    ->native(false)
                                    ->live(),

                                DatePicker::make('custom_date')
                                    ->label('تحديد موعد مخصص')
                                    ->native(false)
                                    ->required(fn($get) => $get('delay_duration') === 'custom')
                                    ->visible(fn($get) => $get('delay_duration') === 'custom')
                                    ->minDate(now())
                                    ->live(),

                                Placeholder::make('next_date_preview')
                                    ->label('موعد الأهلية القادم المتوقع')
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
                                            'custom' => $get('custom_date') ?? 'يرجى اختيار تاريخ',
                                            default => now()->addMonths(3)->format('Y-m-d'),
                                        };
                                    })
                                    ->visible(fn($get) => $get('eligibility_status') === 'temporary')
                                    ->extraAttributes(['class' => 'text-primary-600 font-bold']),

                                Textarea::make('lab_notes')
                                    ->label('ملاحظات المرفق الطبي')
                                    ->placeholder('ملاحظات داخلية حول الحالة أو النتائج...')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->visible(fn(RequestResponse $record) => in_array($record->status, [\App\Enums\RequestResponseStatus::ACCEPTED, \App\Enums\RequestResponseStatus::COMPLETED]))
                    ->action(function (RequestResponse $record, array $data) {
                        $healthProfile = $record->donor->healthProfile;
                        /** @var \App\Models\User $user */
                        $user = Auth::user();
                        $orgId = filament()->getTenant()?->id ?? $user->organization?->id;

                        DB::transaction(function () use ($record, $healthProfile, $data, $orgId) {
                            // 0. Update Response Status (Confirm Arrival & Outcome)
                            $record->verified_at = now();

                            // For ACCEPTED: change status based on eligibility
                            // For COMPLETED: keep as COMPLETED (donation already happened, just update profile)
                            if ($record->status === \App\Enums\RequestResponseStatus::ACCEPTED) {
                                if ($data['eligibility_status'] === 'eligible') {
                                    $record->status = \App\Enums\RequestResponseStatus::COMPLETED;
                                } else {
                                    $record->status = \App\Enums\RequestResponseStatus::DECLINED;
                                }
                            }
                            // If already COMPLETED, status stays COMPLETED regardless of test results

                            $record->save();

                            // 1. Update Health Profile
                            if ($healthProfile) {
                                // Only update verified_blood_type if not already verified, or if same org is re-verifying
                                if (!$healthProfile->verified_blood_type || $healthProfile->verified_by_organization_id === $orgId) {
                                    $healthProfile->verified_blood_type = $data['verified_blood_type'];
                                    $healthProfile->verified_by_organization_id = $orgId;
                                    $healthProfile->verified_at = now();
                                }

                                if ($data['eligibility_status'] === 'permanent') {
                                    $healthProfile->chronic_disease = true;
                                    $healthProfile->is_eligible = false;
                                } elseif ($data['eligibility_status'] === 'temporary') {
                                    $healthProfile->is_eligible = false;

                                    $nextDate = now();
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
                                    // Donation successful - update eligibility and donation date
                                    $healthProfile->is_eligible = true;
                                    $healthProfile->next_eligible_date = null;

                                    // CRITICAL: Update last_donation_date to activate 90-day cooldown
                                    $healthProfile->recent_donation = true; // Must set this or booted() will erase last_donation_date!
                                    $healthProfile->last_donation_date = now();
                                    $healthProfile->total_donations = ($healthProfile->total_donations ?? 0) + 1;
                                }

                                $healthProfile->save();
                            }

                            // 2. Create Eligibility Log
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

                        Notification::make()
                            ->title('تم حفظ النتائج بنجاح')
                            ->success()
                            ->send();
                    }),

                ViewAction::make()
                    ->label('عرض التفاصيل')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->form([
                        Section::make('التفاصيل الطبية الموثقة')
                            ->description('معلومات التحقق المخبري والنتائج الطبية')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Placeholder::make('verified_blood_type_view')
                                            ->label('فصيلة الدم الموثقة')
                                            ->content(fn($record) => $record->donor->healthProfile->verified_blood_type?->getLabel() ?? 'غير موثقة'),

                                        Placeholder::make('verifying_org_view')
                                            ->label('جهة التحقق')
                                            ->content(fn($record) => $record->donor->healthProfile->verifyingOrganization?->org_name ?? 'غير متوفر'),

                                        Placeholder::make('verified_at_view')
                                            ->label('تاريخ التحقق')
                                            ->content(fn($record) => $record->donor->healthProfile->verified_at?->format('Y-m-d H:i') ?? 'غير متوفر'),

                                        Placeholder::make('eligibility_view')
                                            ->label('حالة الأهلية')
                                            ->content(fn($record) => $record->donor->healthProfile->is_eligible ? 'مؤهل للتبرع' : 'غير مؤهل حالياً'),
                                    ]),
                            ]),
                    ]),
            ])
                ->label('الإجراءات')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('primary')
                ->button(),
        ];
    }
}
