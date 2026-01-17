<?php

namespace App\Filament\Organization\Resources\BloodRequests\Pages;

use App\Filament\Organization\Resources\BloodRequests\BloodRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBloodRequest extends ViewRecord
{
    protected static string $resource = BloodRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('generate_qr')
                ->label('QR كود الحملة')
                ->icon('heroicon-o-qr-code')
                ->modalContent(fn($record) => new \Illuminate\Support\HtmlString(
                    '<div class="flex flex-col items-center justify-center p-4 gap-4">
                        <div class="bg-white p-4 rounded-lg shadow-inner">' .
                        \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate(route('donation.checkin', $record)) .
                        '</div>
                        <p class="text-sm text-gray-500 text-center">امسح الكود لتسجيل الحضور والتبرع</p>
                    </div>'
                ))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('إغلاق')
                ->color('info'),
            EditAction::make(),
        ];
    }
}
