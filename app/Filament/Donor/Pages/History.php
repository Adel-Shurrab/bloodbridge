<?php

namespace App\Filament\Donor\Pages;

use App\Enums\RequestResponseStatus;
use App\Models\RequestResponse;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

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
            ->query($this->getTableQuery())
            ->defaultSort('responded_at', 'desc')
            ->columns([
                TextColumn::make('bloodRequest.organization.org_name')
                    ->label('المؤسسة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bloodRequest.blood_type')
                    ->label('فصيلة الطلب')
                    ->badge()
                    ->sortable(),

                TextColumn::make('bloodRequest.units_needed')
                    ->label('الوحدات المطلوبة')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('حالتي')
                    ->badge()
                    ->sortable(),

                TextColumn::make('responded_at')
                    ->label('تاريخ الرد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('verified_at')
                    ->label('وقت التحقق')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('decline_reason')
                    ->label('سبب الاستبعاد/الرفض')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap()
                    ->limit(60)
                    ->visible(fn(?RequestResponse $record) => filled($record?->decline_reason)),

                TextColumn::make('qr_state')
                    ->label('حالة QR')
                    ->badge()
                    ->state(fn(RequestResponse $record) => $record->qr_state_label)
                    ->color(fn(RequestResponse $record) => $record->qr_state_color),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(RequestResponseStatus::class),

                Filter::make('responded_at_range')
                    ->label('نطاق التاريخ')
                    ->form([
                        DatePicker::make('from')->label('من'),
                        DatePicker::make('until')->label('إلى'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn(Builder $q, $date) => $q->whereDate('responded_at', '>=', $date))
                            ->when($data['until'] ?? null, fn(Builder $q, $date) => $q->whereDate('responded_at', '<=', $date));
                    }),
            ])
            ->paginated([10, 25, 50]);
    }

    protected function getTableQuery(): Builder
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return RequestResponse::query()
            ->with(['bloodRequest.organization'])
            ->where('donor_id', $user->donor?->id)
            ->whereNotNull('responded_at')
            // Exclude PENDING (Agreed) requests as they are still active/in-process
            // and displayed on the main Blood Requests page.
            ->where('status', '!=', RequestResponseStatus::PENDING);
    }
}
