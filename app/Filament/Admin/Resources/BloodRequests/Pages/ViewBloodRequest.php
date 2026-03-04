<?php

namespace App\Filament\Admin\Resources\BloodRequests\Pages;

use App\Filament\Admin\Resources\BloodRequests\BloodRequestResource;
use App\Enums\BloodRequestStatus;
use App\Models\BloodRequest;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Carbon\Carbon;

class ViewBloodRequest extends ViewRecord
{
    protected static string $resource = BloodRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
            Action::make('broadcast')
                ->label('بث الطلب')
                ->icon('heroicon-o-megaphone')
                ->color('primary')
                ->visible(fn(BloodRequest $record) => $record->status === BloodRequestStatus::PENDING)
                ->requiresConfirmation()
                ->modalHeading('بث طلب الدم')
                ->modalDescription('سيتم إرسال إشعار لجميع المتبرعين المؤهلين في نطاق البحث. هل تريد المتابعة؟')
                ->modalSubmitActionLabel('نعم، ابدأ البث')
                ->action(function (BloodRequest $record): void {
                    $record->update([
                        'status'         => BloodRequestStatus::BROADCASTED,
                        'broadcasted_at' => now(),
                    ]);

                    Notification::make()
                        ->title('تم بث الطلب بنجاح')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'broadcasted_at']);
                }),

            Action::make('fulfill')
                ->label('تعيين كمكتمل')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn(BloodRequest $record) => $record->status === BloodRequestStatus::BROADCASTED)
                ->requiresConfirmation()
                ->modalHeading('إتمام الطلب')
                ->modalDescription('هل تأكدت أن الوحدات المطلوبة قد تم التبرع بها؟')
                ->modalSubmitActionLabel('نعم، أتمّ الطلب')
                ->action(function (BloodRequest $record): void {
                    $record->update([
                        'status'       => BloodRequestStatus::FULFILLED,
                        'fulfilled_at' => now(),
                    ]);

                    Notification::make()
                        ->title('تم تعيين الطلب كمكتمل')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'fulfilled_at']);
                }),

            Action::make('cancel')
                ->label('إلغاء الطلب')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(BloodRequest $record) => in_array($record->status, [
                    BloodRequestStatus::PENDING,
                    BloodRequestStatus::BROADCASTED,
                ]))
                ->requiresConfirmation()
                ->modalHeading('إلغاء الطلب')
                ->modalDescription('سيتم إلغاء هذا الطلب ولن يظهر للمتبرعين.')
                ->modalSubmitActionLabel('نعم، إلغِ الطلب')
                ->action(function (BloodRequest $record): void {
                    $record->update(['status' => BloodRequestStatus::CANCELLED]);

                    Notification::make()
                        ->title('تم إلغاء الطلب')
                        ->warning()
                        ->send();

                    $this->refreshFormData(['status']);
                }),

            Actions\EditAction::make()->label('تعديل'),
            Actions\DeleteAction::make()->label('حذف'),
            Actions\RestoreAction::make()->label('استعادة'),
            Actions\ForceDeleteAction::make()->label('حذف نهائي'),
        ];
    }
}
