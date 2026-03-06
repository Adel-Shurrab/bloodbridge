<?php

namespace App\Filament\Organization\Resources\BloodRequests\Pages;

use App\Filament\Organization\Resources\BloodRequests\BloodRequestResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateBloodRequest extends CreateRecord
{
    protected static string $resource = BloodRequestResource::class;

    /**
     * Hook to run after creating the blood request
     */
    protected function afterCreate(): void
    {
        try {
            
            $notifiedCount = $this->record->broadcastToEligibleDonors();

            if ($notifiedCount > 0) {
                $expansionInfo = $this->record->wasExpanded()
                    ? "\nنطاق البحث: {$this->record->search_radius_km}كم → {$this->record->actual_search_radius_km}كم"
                    : "\nنطاق البحث: {$this->record->search_radius_km}كم";

                Notification::make()
                    ->title('تم إنشاء الطلب بنجاح ✓')
                    ->body("تم العثور على {$notifiedCount} متبرع محتمل{$expansionInfo}")
                    ->success()
                    ->duration(6000)
                    ->send();
            } else {
                Notification::make()
                    ->title('تم إنشاء الطلب')
                    ->body('لم يتم العثور على متبرعين مناسبين حتى نطاق ' . $this->record->actual_search_radius_km . 'كم. يرجى مراجعة الموقع أو توسيع نطاق البحث.')
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
                ->title('خطأ في معالجة طلب البث')
                ->body('تم إنشاء الطلب ولكن حدث خطأ في عملية البحث عن متبرعين.')
                ->danger()
                ->duration(7000)
                ->send();
        }
    }
}
