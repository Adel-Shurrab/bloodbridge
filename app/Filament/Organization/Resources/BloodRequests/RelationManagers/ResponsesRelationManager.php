<?php

namespace App\Filament\Organization\Resources\BloodRequests\RelationManagers;

use App\Models\RequestResponse;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Enums\BloodType;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'استجابات المتبرعين';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('status')
                    ->label('الحالة')
                    ->options(RequestResponse::getStatusOptions())
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('donor.user.name')
                    ->label('المتبرع')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('donor.healthProfile.blood_type')
                    ->label('الفصيلة المصرحة')
                    ->badge(),

                TextColumn::make('donor.healthProfile.verified_blood_type')
                    ->label('الفصيلة المحققة')
                    ->badge()
                    ->placeholder('لم تحقق بعد'),

                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn($state) => RequestResponse::getStatusOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ((int) $state) {
                        RequestResponse::STATUS_ACCEPTED, RequestResponse::STATUS_COMPLETED => 'success',
                        RequestResponse::STATUS_DECLINED, RequestResponse::STATUS_NO_SHOW => 'danger',
                        default => 'warning',
                    }),

                TextColumn::make('verified_at')
                    ->label('تاريخ التحقق')
                    ->dateTime('Y/m/d h:i A')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(RequestResponse::getStatusOptions()),
            ])
            ->actions([
                // Verification / Completion Action
                Action::make('verify_donation')
                    ->label('تأكيد التبرع')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(RequestResponse $record) => $record->status !== RequestResponse::STATUS_COMPLETED)
                    ->action(function (RequestResponse $record) {
                        DB::transaction(function () use ($record) {
                            $record->status = RequestResponse::STATUS_COMPLETED;
                            $record->verified_at = now();
                            $record->save();

                            // Increment counters in BloodRequest
                            $request = $record->bloodRequest;
                            $request->increment('donors_completed', 1);

                            // Update Request status to FULFILLED if units reached
                            if ($request->donors_completed >= $request->units_needed) {
                                $request->status = BloodRequest::STATUS_FULFILLED;
                                $request->fulfilled_at = now();
                                $request->save();
                            }
                        });

                        Notification::make()
                            ->title('تم تأكيد التبرع بنجاح')
                            ->success()
                            ->send();
                    }),

                // No-Show Action
                Action::make('mark_no_show')
                    ->label('عدم حضور')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(RequestResponse $record) => in_array($record->status, [RequestResponse::STATUS_PENDING, RequestResponse::STATUS_ACCEPTED]))
                    ->action(function (RequestResponse $record) {
                        $record->status = RequestResponse::STATUS_NO_SHOW;
                        $record->save();

                        Notification::make()
                            ->title('تم تسجيل عدم حضور المتبرع')
                            ->warning()
                            ->send();
                    }),

                // Medical Action: Update/Verify Blood Type
                Action::make('verify_blood_type')
                    ->label('تحقق من الفصيلة')
                    ->icon('heroicon-o-beaker')
                    ->color('info')
                    ->form([
                        Select::make('verified_blood_type')
                            ->label('الفصيلة الفعلية (من المختبر)')
                            ->options(BloodType::class)
                            ->required(),
                        Textarea::make('lab_notes')
                            ->label('ملاحظات المخبر')
                            ->placeholder('أدخل أي ملاحظات هامة حول الفصيلة أو العملية...'),
                    ])
                    ->visible(fn(RequestResponse $record) => $record->donor->healthProfile->verified_blood_type === null)
                    ->action(function (RequestResponse $record, array $data) {
                        $healthProfile = $record->donor->healthProfile;

                        if ($healthProfile) {
                            $healthProfile->verified_blood_type = $data['verified_blood_type'];
                            $healthProfile->save();
                        }

                        Notification::make()
                            ->title('تم التحقق من فصيلة الدم وقفلها')
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
            ]);
    }
}
