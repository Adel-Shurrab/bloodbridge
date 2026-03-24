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
                ->label(__('admin.broadcast_request'))
                ->icon('heroicon-o-megaphone')
                ->color('primary')
                ->visible(fn(BloodRequest $record) => $record->status === BloodRequestStatus::PENDING)
                ->requiresConfirmation()
                ->modalHeading(__('admin.broadcast_request_heading'))
                ->modalDescription(__('admin.broadcast_request_description'))
                ->modalSubmitActionLabel(__('admin.yes_start_broadcast'))
                ->action(function (BloodRequest $record): void {
                    $record->update([
                        'status'         => BloodRequestStatus::BROADCASTED,
                        'broadcasted_at' => now(),
                    ]);

                    Notification::make()
                        ->title(__('admin.request_broadcasted_successfully'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'broadcasted_at']);
                }),

            Action::make('fulfill')
                ->label(__('admin.mark_as_fulfilled'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn(BloodRequest $record) => $record->status === BloodRequestStatus::BROADCASTED)
                ->requiresConfirmation()
                ->modalHeading(__('admin.fulfill_request_heading'))
                ->modalDescription(__('admin.fulfill_request_description'))
                ->modalSubmitActionLabel(__('admin.yes_fulfill_request'))
                ->action(function (BloodRequest $record): void {
                    $record->update([
                        'status'       => BloodRequestStatus::FULFILLED,
                        'fulfilled_at' => now(),
                    ]);

                    Notification::make()
                        ->title(__('admin.request_marked_as_fulfilled'))
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'fulfilled_at']);
                }),

            Action::make('cancel')
                ->label(__('admin.cancel_request'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(BloodRequest $record) => in_array($record->status, [
                    BloodRequestStatus::PENDING,
                    BloodRequestStatus::BROADCASTED,
                ]))
                ->requiresConfirmation()
                ->modalHeading(__('admin.cancel_request_heading'))
                ->modalDescription(__('admin.cancel_request_description'))
                ->modalSubmitActionLabel(__('admin.yes_cancel_request'))
                ->action(function (BloodRequest $record): void {
                    $record->update(['status' => BloodRequestStatus::CANCELLED]);

                    Notification::make()
                        ->title(__('admin.request_canceled'))
                        ->warning()
                        ->send();

                    $this->refreshFormData(['status']);
                }),

            Actions\EditAction::make()->label(__('admin.edit')),
            Actions\DeleteAction::make()->label(__('admin.delete')),
            Actions\RestoreAction::make()->label(__('admin.restore')),
            Actions\ForceDeleteAction::make()->label(__('admin.force_delete')),
        ];
    }
}
