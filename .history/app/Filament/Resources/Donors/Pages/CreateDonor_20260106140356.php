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
                    'role' => User::ROLE_DONOR,
                    'is_active' => User::DEFAULT_IS_ACTIVE,
                ]);

                // Set the user_id for the donor
                $data['user_id'] = $user->id;

                // Commit the transaction
                DB::commit();
            } catch (\Exception $e) {
                // Rollback on error
                DB::rollBack();
                throw $e;
            }
        }

        // Remove temporary user creation fields from donor data
        unset(
            $data['user_creation_mode'],
            $data['new_user_name'],
            $data['new_user_email'],
            $data['new_user_phone'],
            $data['new_user_password'],
            $data['new_user_password_confirmation']
        );

        // Calculate next_eligible_date and is_eligible for health profile
        $data = $this->calculateEligibility($data);

        return $data;
    }

    /**
     * Calculate eligibility and next eligible date based on health conditions
     */
    protected function calculateEligibility(array $data): array
    {
        $today = now()->startOfDay();
        $isEligible = true;
        $nextEligibleDate = null;

        // Check infection (14 days)
        if ($data['infection'] ?? false) {
            $isEligible = false;
            $nextEligibleDate = $today->copy()->addDays(14);
        }

        // Check last donation (90 days)
        if (!empty($data['last_donation_date'])) {
            $lastDonation = \Carbon\Carbon::parse($data['last_donation_date'])->startOfDay();
            $daysSince = $lastDonation->diffInDays($today);

            if ($daysSince < 90) {
                $isEligible = false;
                $futureDate = $lastDonation->copy()->addDays(90);

                if (!$nextEligibleDate || $futureDate > $nextEligibleDate) {
                    $nextEligibleDate = $futureDate;
                }
            }
        }

        // Check surgery (28 days)
        if (!empty($data['surgery_date'])) {
            $surgery = \Carbon\Carbon::parse($data['surgery_date'])->startOfDay();
            $daysSince = $surgery->diffInDays($today);

            if ($daysSince < 28) {
                $isEligible = false;
                $futureDate = $surgery->copy()->addDays(28);

                if (!$nextEligibleDate || $futureDate > $nextEligibleDate) {
                    $nextEligibleDate = $futureDate;
                }
            }
        }

        // Set the calculated values
        $data['is_eligible'] = $isEligible;
        $data['next_eligible_date'] = $nextEligibleDate ? $nextEligibleDate->toDateString() : null;

        return $data;
    }
}
