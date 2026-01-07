<?php

namespace App\Filament\Resources\Donors\Pages;

use App\Filament\Resources\Donors\DonorResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateDonor extends CreateRecord
{
    protected static string $resource = DonorResource::class;

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
                    'role' => User::ROLE_DONOR,
                    'is_active' => User::DEFAULT_IS_ACTIVE,
                ]);

                $data['user_id'] = $user->id;

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
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
