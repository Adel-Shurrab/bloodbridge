<?php

namespace App\Filament\Resources\Organizations\Pages;

use App\Filament\Resources\Organizations\OrganizationResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check if we're creating a new user
        if (isset($data['user_creation_mode']) && $data['user_creation_mode'] === 'create') {
            // Create the user within a transaction
            DB::beginTransaction();

            try {
                // Create the new user
                $user = User::create([
                    'name' => $data['new_user_name'],
                    'email' => $data['new_user_email'],
                    'phone' => $data['new_user_phone'],
                    'password' => Hash::make($data['new_user_password']),
                    'role' => User::ROLE_ORGANIZATION,
                    'is_active' => User::DEFAULT_IS_ACTIVE,
                ]);

                // Set the user_id for the organization
                $data['user_id'] = $user->id;

                // Sync responsible person data from new user
                $data['responsible_person_name'] = $user->name;
                $data['responsible_person_email'] = $user->email;

                // Commit the transaction
                DB::commit();
            } catch (\Exception $e) {
                // Rollback on error
                DB::rollBack();
                throw $e;
            }
        } elseif (isset($data['user_id'])) {
            // Sync responsible person data from existing user
            $user = User::find($data['user_id']);
            if ($user) {
                $data['responsible_person_name'] = $user->name;
                $data['responsible_person_email'] = $user->email;
            }
        }

        // Remove temporary user creation fields from organization data
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
