<?php

namespace App\Filament\Admin\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use Filament\Actions\DeleteAction;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;


class EditOrganization extends EditRecord
{
    use Translatable;

    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            DeleteAction::make()->label(__('Delete')),
            \Filament\Actions\RestoreAction::make()->label(__('Restore')),
            \Filament\Actions\ForceDeleteAction::make()->label(__('Force Delete')),
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
