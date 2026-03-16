<?php

namespace App\Filament\Admin\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use App\Models\User;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateOrganization extends CreateRecord
{
    use Translatable;

    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['user_id'])) {
            $user = User::query()->find($data['user_id']);

            if ($user) {
                $data['responsible_person_name'] = $user->name;
                $data['responsible_person_email'] = $user->email;
            }
        }

        return $data;
    }
}
