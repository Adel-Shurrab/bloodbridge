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
            // Broadcast to eligible donors
            $notifiedCount = $this->record->broadcastToEligibleDonors();

            // Show success notification
            if ($notifiedCount > 0) {
                Notification::make()
                    ->title('تم إنشاء الطلب بنجاح')
                    ->body("تم العثور على {$notifiedCount} متبرع محتمل في النطاق المحدد")
                    ->success()
                    ->duration(5000)
                    ->send();
            } else {
                Notification::make()
                    ->title('تم إنشاء الطلب')
                    ->body('لم يتم العثور على متبرعين مناسبين في النطاق المحدد. يرجى مراجعة نطاق البحث أو الموقع.')
                    ->warning()
                    ->duration(7000)
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
