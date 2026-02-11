<?php

namespace App\Filament\Donor\Pages;

use App\Enums\BloodType;
use App\Enums\RequestResponseStatus;
use App\Models\RequestResponse;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class History extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'السجل';
    protected static ?string $title = 'تاريخ التبرعات';
    protected static ?int $navigationSort = 30;

    public function getView(): string
    {
        return 'filament.donor.pages.history';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->defaultSort('responded_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('bloodRequest.organization.org_name')
                    ->label('المؤسسة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bloodRequest.blood_type')
                    ->label('فصيلة الطلب')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $this->asBloodType($state)?->getLabel() ?? (string) $state)
                    ->color(fn ($state) => $this->asBloodType($state)?->getColor() ?? 'gray'),

                Tables\Columns\TextColumn::make('bloodRequest.units_needed')
                    ->label('الوحدات المطلوبة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('حالتي')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $this->asResponseStatus($state)?->getLabel() ?? (string) $state)
                    ->color(fn ($state) => $this->asResponseStatus($state)?->getColor() ?? 'gray'),

                Tables\Columns\TextColumn::make('responded_at')
                    ->label('تاريخ الرد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('verified_at')
                    ->label('وقت التحقق')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('decline_reason')
                    ->label('سبب الاستبعاد/الرفض')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap()
                    ->limit(60)
                    ->visible(fn (?RequestResponse $record) => filled($record?->decline_reason)),

                Tables\Columns\TextColumn::make('qr_state')
                    ->label('حالة QR')
                    ->badge()
                    ->state(function (?RequestResponse $record): string {
                        if (!$record) {
                            return 'غير متوفر';
                        }

                        if (blank($record->verification_qr_code)) {
                            return 'غير متوفر';
                        }

                        if (filled($record->verified_at)) {
                            return 'تم الاستخدام';
                        }

                        if (filled($record->qr_code_expires_at) && now()->greaterThan($record->qr_code_expires_at)) {
                            return 'منتهي';
                        }

                        return 'فعّال';
                    })
                    ->color(function (?RequestResponse $record): string {
                        if (!$record) {
                            return 'gray';
                        }

                        if (blank($record->verification_qr_code)) {
                            return 'gray';
                        }

                        if (filled($record->verified_at)) {
                            return 'success';
                        }

                        if (filled($record->qr_code_expires_at) && now()->greaterThan($record->qr_code_expires_at)) {
                            return 'danger';
                        }

                        return 'warning';
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(
                        collect(RequestResponseStatus::cases())
                            ->mapWithKeys(fn (RequestResponseStatus $case) => [$case->value => $case->getLabel()])
                            ->all()
                    ),

                Tables\Filters\Filter::make('responded_at_range')
                    ->label('نطاق التاريخ')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('من'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('إلى'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('responded_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('responded_at', '<=', $date));
                    }),
            ])
            ->paginated([10, 25, 50]);
    }

    protected function getQuery(): Builder
    {
        $userId = auth()->id();

        return RequestResponse::query()
            ->with(['bloodRequest.organization'])
            ->whereHas('donor', fn (Builder $q) => $q->where('user_id', $userId))
            ->whereNotNull('responded_at');
    }

    private function asBloodType(mixed $value): ?BloodType
    {
        if ($value instanceof BloodType) {
            return $value;
        }

        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }

        return is_numeric($value) ? BloodType::tryFrom((int) $value) : null;
    }

    private function asResponseStatus(mixed $value): ?RequestResponseStatus
    {
        if ($value instanceof RequestResponseStatus) {
            return $value;
        }

        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }

        return is_numeric($value) ? RequestResponseStatus::tryFrom((int) $value) : null;
    }
}
