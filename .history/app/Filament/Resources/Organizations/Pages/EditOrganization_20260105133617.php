<?php

namespace App\Filament\Resources\Organizations\Pages;

use App\Filament\Resources\Organizations\OrganizationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrganization extends EditRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
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
            $record->user->update([
                'name' => $data['user_name'] ?? $record->user->name,
                'email' => $data['user_email'] ?? $record->user->email,
            ]);
        }

        return $record;
    }
}
