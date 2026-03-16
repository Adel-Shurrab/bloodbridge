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
                'message' => __('Eligibility data unavailable'),
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
                'message' => __('You are eligible to donate now'),
                'timestamp' => null,
            ];
        }

        if (! $profile->next_eligible_date) {
            return [
                'eligible_now' => false,
                'message' => __('Sorry, you are currently ineligible to donate for medical reasons. For your safety, please follow up with the relevant authority.'),
                'timestamp' => null,
            ];
        }

        $target = $profile->next_eligible_date->startOfDay();

        return [
            'eligible_now' => false,
            'message' => __('Countdown to eligibility'),
            'timestamp' => $target->timestamp,
        ];
    }
}

