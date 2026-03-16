<?php

namespace App\Filament\Admin\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use App\Enums\OrganizationStatus;
use App\Models\Organization;
use Filament\Actions;
use Filament\Actions\Action;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\ViewRecord\Concerns\Translatable;


class ViewOrganization extends ViewRecord
{
    use Translatable;

    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label(__('Approve Organization'))
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn(Organization $record) => $record->approval_status === OrganizationStatus::PENDING)
                ->requiresConfirmation()
                ->modalHeading(__('Approve Section Heading'))
                ->modalDescription(__('Approve Section Description'))
                ->modalSubmitActionLabel(__('Yes, Approve'))
                ->action(function (Organization $record): void {
                    $record->update([
                        'approval_status' => OrganizationStatus::APPROVED,
                        'approved_at'     => now(),
                        'approved_by'     => auth()->id(),
                    ]);

                    Notification::make()
                        ->title(__('Organization Approved Successfully'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['approval_status', 'approved_at', 'approved_by']);
                }),

            Action::make('reject')
                ->label(__('Reject Organization'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(Organization $record) => $record->approval_status === OrganizationStatus::PENDING)
                ->requiresConfirmation()
                ->modalHeading(__('Reject Section Heading'))
                ->modalDescription(__('Reject Section Description'))
                ->modalSubmitActionLabel(__('Confirm Rejection'))
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label(__('Rejection Reason'))
                        ->required()
                        ->rows(3),
                ])
                ->action(function (Organization $record, array $data): void {
                    $record->update([
                        'approval_status'  => OrganizationStatus::REJECTED,
                        'rejection_reason' => $data['rejection_reason'],
                    ]);

                    Notification::make()
                        ->title(__('Organization Rejected'))
                        ->danger()
                        ->send();

                    $this->refreshFormData(['approval_status', 'rejection_reason']);
                }),

            Actions\EditAction::make()->label(__('Edit')),
            Actions\DeleteAction::make()->label(__('Delete')),
            Actions\RestoreAction::make()->label(__('Restore')),
            Actions\ForceDeleteAction::make()->label(__('Force Delete')),
        ];
    }
}
