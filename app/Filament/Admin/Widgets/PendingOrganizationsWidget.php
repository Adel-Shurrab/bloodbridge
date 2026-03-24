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
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('admin.widget_pending_organizations_heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn() => Organization::where('approval_status', '=', \App\Enums\OrganizationStatus::PENDING, 'and')
            )
            ->modelLabel(__('admin.organization'))
            ->pluralModelLabel(__('admin.organizations'))
            ->columns([
                Tables\Columns\TextColumn::make('org_name')
                    ->label(__('admin.organization_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('responsible_person_name')
                    ->label(__('admin.responsible_person'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_email')
                    ->label(__('admin.email')),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->label(__('admin.phone_number')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.registration_date'))
                    ->dateTime('Y/m/d')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('admin.view'))
                    ->form(fn($form) => OrganizationResource::form($form))
                    ->modalWidth('7xl'),
                Action::make('approve')
                    ->label(__('admin.approve'))
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Organization $record) {
                        $record->approval_status = \App\Enums\OrganizationStatus::APPROVED;
                        $record->save();

                        Notification::make()
                            ->title(__('admin.approved_successfully'))
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label(__('admin.reject'))
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Organization $record) {
                        $record->approval_status = \App\Enums\OrganizationStatus::REJECTED;
                        $record->save();

                        Notification::make()
                            ->title(__('admin.organization_rejected'))
                            ->danger()
                            ->send();
                    }),
            ])
            ->emptyStateHeading(__('admin.no_organizations'))
            ->emptyStateDescription(__('admin.no_organizations_pending'));
    }
}
