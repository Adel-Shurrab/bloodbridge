<?php

namespace App\Filament\Donor\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DonorLatestAchievementWidget extends Widget
{
    protected string $view = 'filament.donor.widgets.donor-latest-achievement-widget';
    protected int|string|array $columnSpan = 'full';

    public function getLatestAchievementData(): array
    {
        $donor = Auth::user()?->donor;

        if (! $donor) {
            return ['has_achievement' => false, 'points' => 0, 'level' => 1, 'total' => 0];
        }

        $latest = $donor->donorAchievements()
            ->with('achievement')
            ->orderBy('earned_at', 'desc')
            ->first();

        return [
            'has_achievement' => $latest !== null,
            'latest'          => $latest,
            'points'          => (int) $donor->points,
            'level'           => (int) $donor->level,
            'total'           => $donor->donorAchievements()->count(),
        ];
    }
}
