<?php

namespace App\Filament\Admin\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrganization extends EditRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('حذف'),
            \Filament\Actions\RestoreAction::make()->label('استعادة'),
            \Filament\Actions\ForceDeleteAction::make()->label('حذف نهائي'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record->user;

        if ($user) {
            $data['user_name'] = $user->name;
            $data['user_email'] = $user->email;
        }

        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $record->update($data);

        if ($record->user) {
            $user = $record->user;
            $user->name = $data['user_name'] ?? $user->name;
            $user->email = $data['user_email'] ?? $user->email;
            $user->save();
        }

        return $record;
    }
}
