<?php

namespace App\Filament\Donor\Pages;

use App\Enums\RequestResponseStatus;
use App\Models\RequestResponse;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use LaraZeus\SpatieTranslatable\Resources\Concerns\HasActiveLocaleSwitcher;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class History extends Page implements HasTable
{
    use InteractsWithTable, HasActiveLocaleSwitcher {
        HasActiveLocaleSwitcher::getActiveFormsLocale insteadof InteractsWithTable;
        HasActiveLocaleSwitcher::getActiveActionsLocale insteadof InteractsWithTable;
        HasActiveLocaleSwitcher::getFilamentTranslatableContentDriver insteadof InteractsWithTable;
    }

    public ?string $activeLocale = null;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    public static function getNavigationLabel(): string
    {
        return __('donor.donation_history');
    }
    public function getTitle(): string
    {
        return __('donor.donation_history');
    }
    protected static ?int $navigationSort = 30;

    protected function getHeaderActions(): array
    {
        return [];
    }

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
                    ->label(__('donor.organization'))
                    ->getStateUsing(fn($record) => $record->bloodRequest?->organization ? ($record->bloodRequest->organization->getTranslation('org_name', app()->getLocale(), false) ?: $record->bloodRequest->organization->getTranslation('org_name', 'ar', false)) : null)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bloodRequest.blood_type')
                    ->label(__('donor.requested_blood_type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('bloodRequest.units_needed')
                    ->label(__('donor.units_needed'))
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('donor.my_status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('responded_at')
                    ->label(__('donor.response_date'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('verified_at')
                    ->label(__('donor.verification_time'))
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

                TextColumn::make('decline_reason')
                    ->label(__('donor.decline_rejection_reason'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap()
                    ->limit(60)
                    ->visible(fn(?RequestResponse $record) => filled($record?->decline_reason)),

                TextColumn::make('qr_state')
                    ->label(__('donor.qr_status'))
                    ->badge()
                    ->state(fn(RequestResponse $record) => $record->qr_state_label)
                    ->color(fn(RequestResponse $record) => $record->qr_state_color),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('donor.status'))
                    ->options(RequestResponseStatus::class),

                Filter::make('responded_at_range')
                    ->label(__('donor.date_range'))
                    ->form([
                        DatePicker::make('from')->label(__('donor.from')),
                        DatePicker::make('until')->label(__('donor.to')),
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
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return RequestResponse::query()
            ->with(['bloodRequest.organization'])
            ->where('donor_id', $user->donor?->id)
            ->whereNotNull('responded_at')

            ->where('status', '!=', RequestResponseStatus::PENDING);
    }
}
