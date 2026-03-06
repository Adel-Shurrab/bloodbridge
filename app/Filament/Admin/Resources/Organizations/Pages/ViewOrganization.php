<?php

namespace App\Filament\Admin\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use App\Enums\OrganizationStatus;
use App\Models\Organization;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
            Action::make('approve')
                ->label('اعتماد المنظمة')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn(Organization $record) => $record->approval_status === OrganizationStatus::PENDING)
                ->requiresConfirmation()
                ->modalHeading('اعتماد المنظمة')
                ->modalDescription('هل أنت متأكد من اعتماد هذه المنظمة؟ سيتمكن مستخدمها من الدخول وإنشاء طلبات الدم.')
                ->modalSubmitActionLabel('نعم، اعتمد')
                ->action(function (Organization $record): void {
                    $record->update([
                        'approval_status' => OrganizationStatus::APPROVED,
                        'approved_at'     => now(),
                        'approved_by'     => auth()->id(),
                    ]);

                    Notification::make()
                        ->title('تم اعتماد المنظمة بنجاح')
                        ->success()
                        ->send();

                    $this->refreshFormData(['approval_status', 'approved_at', 'approved_by']);
                }),

            Action::make('reject')
                ->label('رفض المنظمة')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn(Organization $record) => $record->approval_status === OrganizationStatus::PENDING)
                ->requiresConfirmation()
                ->modalHeading('رفض المنظمة')
                ->modalDescription('سيتم رفض طلب هذه المنظمة. يُرجى إدخال سبب الرفض.')
                ->modalSubmitActionLabel('تأكيد الرفض')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label('سبب الرفض')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (Organization $record, array $data): void {
                    $record->update([
                        'approval_status'  => OrganizationStatus::REJECTED,
                        'rejection_reason' => $data['rejection_reason'],
                    ]);

                    Notification::make()
                        ->title('تم رفض المنظمة')
                        ->danger()
                        ->send();

                    $this->refreshFormData(['approval_status', 'rejection_reason']);
                }),

            Actions\EditAction::make()->label('تعديل'),
            Actions\DeleteAction::make()->label('حذف'),
            Actions\RestoreAction::make()->label('استعادة'),
            Actions\ForceDeleteAction::make()->label('حذف نهائي'),
        ];
    }
}
