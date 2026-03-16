<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Organization;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use Filament\Actions\ViewAction;

class PendingOrganizationsWidget extends TableWidget
{
    protected static ?string $heading = 'filament.widgets.pending-organizations.heading';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn() => Organization::where('approval_status', '=', \App\Enums\OrganizationStatus::PENDING, 'and')
            )
            ->modelLabel(__('Organization'))
            ->pluralModelLabel(__('Organizations'))
            ->columns([
                Tables\Columns\TextColumn::make('org_name')
                    ->label(__('Organization Name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_person_name')
                    ->label(__('Responsible Person'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_email')
                    ->label(__('Email')),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->label(__('Phone Number')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Registration Date'))
                    ->dateTime('Y/m/d')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('View'))
                    ->form(fn($form) => OrganizationResource::form($form))
                    ->modalWidth('7xl'),
                Action::make('approve')
                    ->label(__('Approve'))
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Organization $record) {
                        $record->approval_status = \App\Enums\OrganizationStatus::APPROVED;
                        $record->save();

                        Notification::make()
                            ->title(__('Approved successfully'))
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label(__('Reject'))
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Organization $record) {
                        $record->approval_status = \App\Enums\OrganizationStatus::REJECTED;
                        $record->save();

                        Notification::make()
                            ->title(__('Organization rejected'))
                            ->danger()
                            ->send();
                    }),
            ])
            ->emptyStateHeading(__('No organizations'))
            ->emptyStateDescription(__('No organizations pending approval at the moment'));
    }
}
