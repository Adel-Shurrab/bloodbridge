<?php

namespace App\Filament\Admin\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        
        if (isset($data['user_creation_mode']) && $data['user_creation_mode'] === 'create') {
            
            DB::beginTransaction();

            try {
                
                $user = User::create([
                    'name' => $data['new_user_name'],
                    'email' => $data['new_user_email'],
                    'phone' => $data['new_user_phone'],
                    'password' => Hash::make($data['new_user_password']),
                    'role' => \App\Enums\UserRole::ORGANIZATION,
                    'is_active' => User::DEFAULT_IS_ACTIVE,
                ]);

                $data['user_id'] = $user->id;

                $data['responsible_person_name'] = $user->name;
                $data['responsible_person_email'] = $user->email;

                DB::commit();
            } catch (\Exception $e) {
                
                DB::rollBack();
                throw $e;
            }
        } elseif (isset($data['user_id'])) {
            
            $user = User::find($data['user_id']);
            if ($user) {
                $data['responsible_person_name'] = $user->name;
                $data['responsible_person_email'] = $user->email;
            }
        }

        unset(
            $data['user_creation_mode'],
            $data['new_user_name'],
            $data['new_user_email'],
            $data['new_user_phone'],
            $data['new_user_password'],
            $data['new_user_password_confirmation']
        );

        return $data;
    }
}
