<?php

namespace App\Filament\Organization\Resources\BloodRequests\Pages;

use App\Filament\Organization\Resources\BloodRequests\BloodRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBloodRequest extends CreateRecord
{
    protected static string $resource = BloodRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = auth()->user()->organization?->id;

        return $data;
    }
}
