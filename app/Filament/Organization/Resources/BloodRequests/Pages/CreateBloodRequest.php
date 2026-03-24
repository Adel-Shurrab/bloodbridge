<?php

namespace App\Filament\Organization\Resources\BloodRequests\Pages;

use App\Filament\Organization\Resources\BloodRequests\BloodRequestResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateBloodRequest extends CreateRecord
{
    use Translatable;

    protected static string $resource = BloodRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function afterCreate(): void
    {
        try {
            $notifiedCount = $this->record->broadcastToEligibleDonors();

            if ($notifiedCount > 0) {
                $expansionInfo = $this->record->actual_search_radius_km > $this->record->search_radius_km
                    ? __('organization.search_radius_expanded', [
                        'radius' => $this->record->search_radius_km,
                        'actualRadius' => $this->record->actual_search_radius_km,
                    ])
                    : __('organization.search_radius_single', [
                        'radius' => $this->record->search_radius_km,
                    ]);

                Notification::make()
                    ->success()
                    ->title(__('organization.request_created_successfully'))
                    ->body(__('organization.found_potential_donors', [
                        'count' => $notifiedCount,
                        'expansionInfo' => $expansionInfo,
                    ]))
                    ->duration(6000)
                    ->send();
            } else {
                Notification::make()
                    ->warning()
                    ->title(__('organization.request_created'))
                    ->body(__('organization.no_suitable_donors_found', [
                        'radius' => $this->record->actual_search_radius_km,
                    ]))
                    ->duration(8000)
                    ->send();
            }
        } catch (\Exception $e) {
            Log::error('Failed to broadcast blood request after creation', [
                'blood_request_id' => $this->record->id,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('organization.error_processing_broadcast_request'))
                ->body(__('organization.request_created_but_broadcast_failed'))
                ->duration(7000)
                ->send();
        }
    }
}
