<?php

namespace App\Filament\Admin\Resources\BloodRequests\Pages;

use App\Filament\Admin\Resources\BloodRequests\BloodRequestResource;
use App\Enums\BloodRequestStatus;
use App\Models\BloodRequest;
use Filament\Actions;
use Filament\Actions\Action;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\ViewRecord\Concerns\Translatable;

use Carbon\Carbon;

class ViewBloodRequest extends ViewRecord
{
    use Translatable;

    protected static string $resource = BloodRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('broadcast')
                ->label(__('Broadcast Request'))
                ->icon('heroicon-o-megaphone')
                ->color('primary')
                ->visible(fn(BloodRequest $record) => $record->status === BloodRequestStatus::PENDING)
                ->requiresConfirmation()
                ->modalHeading(__('Broadcast Request Heading'))
                ->modalDescription(__('Broadcast Request Description'))
                ->modalSubmitActionLabel(__('Yes, Start Broadcast'))
                ->action(function (BloodRequest $record): void {
                    $record->update([
                        'status'         => BloodRequestStatus::BROADCASTED,
                        'broadcasted_at' => now(),
                    ]);

                    Notification::make()
                        ->title(__('Request Broadcasted Successfully'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'broadcasted_at']);
                }),

            Action::make('fulfill')
                ->label(__('Mark as Fulfilled'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn(BloodRequest $record) => $record->status === BloodRequestStatus::BROADCASTED)
                ->requiresConfirmation()
                ->modalHeading(__('Fulfill Request Heading'))
                ->modalDescription(__('Fulfill Request Description'))
                ->modalSubmitActionLabel(__('Yes, Fulfill Request'))
                ->action(function (BloodRequest $record): void {
                    $record->update([
                        'status'       => BloodRequestStatus::FULFILLED,
                        'fulfilled_at' => now(),
                    ]);

                    Notification::make()
                        ->title(__('Request Marked as Fulfilled'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'fulfilled_at']);
                }),

            Action::make('cancel')
                ->label(__('Cancel Request'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(BloodRequest $record) => in_array($record->status, [
                    BloodRequestStatus::PENDING,
                    BloodRequestStatus::BROADCASTED,
                ]))
                ->requiresConfirmation()
                ->modalHeading(__('Cancel Request Heading'))
                ->modalDescription(__('Cancel Request Description'))
                ->modalSubmitActionLabel(__('Yes, Cancel Request'))
                ->action(function (BloodRequest $record): void {
                    $record->update(['status' => BloodRequestStatus::CANCELLED]);

                    Notification::make()
                        ->title(__('Request Canceled'))
                        ->warning()
                        ->send();

                    $this->refreshFormData(['status']);
                }),

            Actions\EditAction::make()->label(__('Edit')),
            Actions\DeleteAction::make()->label(__('Delete')),
            Actions\RestoreAction::make()->label(__('Restore')),
            Actions\ForceDeleteAction::make()->label(__('Force Delete')),
        ];
    }
}
