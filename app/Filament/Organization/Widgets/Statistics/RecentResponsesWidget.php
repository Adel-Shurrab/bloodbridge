<?php

namespace App\Filament\Organization\Widgets\Statistics;

use App\Models\Organization;
use App\Models\RequestResponse;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class RecentResponsesWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 10;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $organization = $this->getOrganization();

        return $table
            ->heading(__('organization.latest_responses'))
            ->description(__('organization.latest_5_donor_responses'))
            ->query(
                RequestResponse::query()
                    ->when(
                        $organization,
                        fn($query) => $query->whereHas('bloodRequest', function ($bloodRequestQuery) use ($organization) {
                            $bloodRequestQuery->where('organization_id', $organization->id);
                        }),
                        fn($query) => $query->whereRaw('1 = 0')
                    )
                    ->with(['donor.user', 'bloodRequest'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('donor.user.name')
                    ->label(__('organization.donor_name'))
                    ->getStateUsing(fn($record) => $record->donor?->user?->name)
                    ->searchable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('bloodRequest.blood_type')
                    ->label(__('organization.blood_type'))
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->getLabel()),

                BadgeColumn::make('status')
                    ->label(__('organization.status'))
                    ->formatStateUsing(fn($state) => $state->getLabel())
                    ->colors([
                        'warning' => \App\Enums\RequestResponseStatus::PENDING->value,
                        'success' => \App\Enums\RequestResponseStatus::ACCEPTED->value,
                        'primary' => \App\Enums\RequestResponseStatus::COMPLETED->value,
                        'danger' => \App\Enums\RequestResponseStatus::DECLINED->value,
                        'gray' => \App\Enums\RequestResponseStatus::UNREACHABLE->value,
                    ]),

                TextColumn::make('created_at')
                    ->label(__('organization.response_date'))
                    ->dateTime('Y-m-d H:i')
                    ->since()
                    ->icon('heroicon-o-clock'),
            ])
            ->actions([
                ViewAction::make('view')
                    ->label(__('organization.view_details'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(__('organization.donor_response_details'))
                    ->form([
                        Section::make(__('organization.donor_information'))
                            ->compact()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Placeholder::make('donor_name')
                                            ->label(__('organization.donor_name'))
                                            ->content(fn($record) => $record->donor?->user?->name),
                                        Placeholder::make('donor_phone')
                                            ->label(__('organization.phone_number'))
                                            ->content(fn($record) => $record->donor?->user?->phone ?? __('organization.not_available')),
                                    ]),
                            ]),
                        Section::make(__('organization.blood_type_and_verification_details'))
                            ->compact()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Placeholder::make('blood_type')
                                            ->label(__('organization.blood_type_self_reported'))
                                            ->content(fn($record) => $record->donor?->healthProfile?->blood_type?->getLabel() ?? __('organization.unknown')),
                                        Placeholder::make('verified_blood_type')
                                            ->label(__('organization.confirmed_blood_type'))
                                            ->content(fn($record) => $record->donor?->healthProfile?->verified_blood_type?->getLabel() ?? __('organization.not_lab_verified')),
                                    ]),
                            ]),
                        Section::make(__('organization.status_and_timing'))
                            ->compact()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Placeholder::make('status')
                                            ->label(__('organization.response_status'))
                                            ->content(fn($record) => $record->status->getLabel()),
                                        Placeholder::make('created_at')
                                            ->label(__('organization.response_time'))
                                            ->content(fn($record) => $record->created_at->format('Y-m-d H:i')),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    protected function getOrganization(): ?Organization
    {
        $tenant = filament()->getTenant();

        if ($tenant instanceof Organization) {
            return $tenant;
        }

        return Auth::user()?->organization;
    }
}
