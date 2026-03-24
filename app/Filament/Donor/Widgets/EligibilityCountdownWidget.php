<?php

namespace App\Filament\Donor\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class EligibilityCountdownWidget extends Widget
{
    protected string $view = 'filament.donor.widgets.eligibility-countdown';

    protected int|string|array $columnSpan = 'full';

    public function getEligibilityData(): array
    {
        $user = Auth::user();
        $donor = $user?->donor;
        $profile = $donor?->healthProfile;

        if (! $profile) {
            return [
                'eligible_now' => false,
                'message' => __('donor.eligibility_data_unavailable'),
                'timestamp' => null,
            ];
        }

        $dateHasPassed = $profile->next_eligible_date !== null
            && (
                $profile->next_eligible_date->startOfDay()->isPast()
                || $profile->next_eligible_date->startOfDay()->isToday()
            );

        if ($profile->is_eligible || $dateHasPassed) {
            return [
                'eligible_now' => true,
                'message' => __('donor.eligible_now'),
                'timestamp' => null,
            ];
        }

        if (! $profile->next_eligible_date) {
            return [
                'eligible_now' => false,
                'message' => __('donor.ineligible_medical'),
                'timestamp' => null,
            ];
        }

        $target = $profile->next_eligible_date->startOfDay();

        return [
            'eligible_now' => false,
            'message' => __('donor.countdown_to_eligibility'),
            'timestamp' => $target->timestamp,
        ];
    }
}
