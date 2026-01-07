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

        // Calculate eligibility and next eligible date
        $data = $this->calculateEligibility($data);

        return $data;
    }

    /**
     * Calculate eligibility based on health profile
     */
    private function calculateEligibility(array $data): array
    {
        $today = now()->startOfDay();
        $isEligible = true;
        $nextEligibleDate = null;

        // 1. Infection (Temporary 14 days)
        if ($data['infection'] ?? false) {
            $isEligible = false;
            $nextEligibleDate = $today->copy()->addDays(14);
        }

        // 2. Donation Logic (90 Days)
        if (!empty($data['last_donation_date'])) {
            $lastDonation = \Carbon\Carbon::parse($data['last_donation_date'])->startOfDay();
            $daysSince = $lastDonation->diffInDays($today);

            if ($daysSince < 90) {
                $isEligible = false;
                $futureDate = $lastDonation->copy()->addDays(90);

                // Keep the furthest date
                if (!$nextEligibleDate || $futureDate > $nextEligibleDate) {
                    $nextEligibleDate = $futureDate;
                }
            }
        }

        // 3. Surgery Logic (28 Days)
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

        // Set calculated values
        $data['is_eligible'] = $isEligible;
        $data['next_eligible_date'] = $nextEligibleDate?->format('Y-m-d');

        return $data;
    }
}
