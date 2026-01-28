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

        // حماية
        if (! $profile) {
            return [
                'eligible_now' => false,
                'message' => 'بيانات الأهلية غير متوفرة',
                'timestamp' => null,
            ];
        }

        // مؤهل الآن
        if ($profile->is_eligible) {
            return [
                'eligible_now' => true,
                'message' => 'أنت مؤهل للتبرع الآن',
                'timestamp' => null,
            ];
        }

        // غير مؤهل + لا يوجد تاريخ
        if (! $profile->next_eligible_date) {
            return [
                'eligible_now' => false,
                'message' => 'نعتذر، أنت غير مؤهل للتبرع حاليًا لأسباب صحية. حرصًا على سلامتك، يرجى متابعة الجهة المختصة.',
                'timestamp' => null,
            ];
        }

        // غير مؤهل + يوجد تاريخ
        $target = $profile->next_eligible_date->startOfDay();

        return [
            'eligible_now' => false,
            'message' => 'العد التنازلي حتى الأهلية',
            'timestamp' => $target->timestamp,
        ];
    }
}
