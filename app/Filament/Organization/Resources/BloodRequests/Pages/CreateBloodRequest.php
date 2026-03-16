<?php

namespace App\Filament\Organization\Resources\BloodRequests\Pages;

use App\Filament\Organization\Resources\BloodRequests\BloodRequestResource;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

use Illuminate\Support\Facades\Log;

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

    /**
     * Hook to run after creating the blood request
     */
    protected function afterCreate(): void
    {
        try {
            
            $notifiedCount = $this->record->broadcastToEligibleDonors();

            if ($notifiedCount > 0) {
                $expansionInfo = $this->record->actual_search_radius_km > $this->record->search_radius_km
                ? __("\nSearch radius: :radius km → :actualRadius km", [
                    'radius' => $this->record->search_radius_km,
                    'actualRadius' => $this->record->actual_search_radius_km
                ])
                : __("\nSearch radius: :radius km", ['radius' => $this->record->search_radius_km]);

                Notification::make()
                    ->success()
                    ->title(__('Request created successfully ✓'))
                    ->body(__("Found :count potential donors:expansionInfo", [
                        'count' => $notifiedCount,
                        'expansionInfo' => $expansionInfo
                    ]))
                    ->success()
                    ->duration(6000)
                    ->send();
            } else {
                Notification::make()
                    ->warning()
                    ->title(__('Request Created'))
                    ->body(__('No suitable donors found up to :radius km range. Please review location or expand search radius.', [
                        'radius' => $this->record->actual_search_radius_km
                    ]))
                    ->warning()
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
                ->title(__('Error processing broadcast request'))
                ->body(__('Request created but error occurred during donor search process.'))
                ->danger()
                ->duration(7000)
                ->send();
        }
    }
}

