<?php

namespace App\Filament\Donor\Pages;

use App\Models\Achievement;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Achievements extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';
    protected static ?int $navigationSort = 20; // between Blood Requests and History
    protected string $view = 'filament.donor.pages.achievements';

    public static function getNavigationLabel(): string { return __('donor.achievements'); }
    public function getTitle(): string                  { return __('donor.achievements'); }
    public function getHeading(): string                { return ''; } // suppress default heading

    public function getAchievementsData(): array
    {
        $donor = Auth::user()?->donor;

        if (! $donor) {
            return ['earned' => [], 'locked' => [], 'points' => 0, 'level' => 1];
        }

        $earnedRows = $donor->donorAchievements()
            ->with('achievement')
            ->orderBy('earned_at', 'desc')
            ->get();

        $earnedIds = $earnedRows->pluck('achievement_id');

        $locked = Achievement::whereNotIn('id', $earnedIds)
            ->orderBy('display_order')
            ->get()
            ->map(function (Achievement $a) use ($donor) {
                $current = match ($a->criteria_type) {
                    'donations' => $donor->healthProfile?->total_donations ?? 0,
                    'points'    => $donor->points,
                    default     => 0,
                };
                $target   = $a->criteria_value;
                $progress = $target > 0 ? min(100, (int) round(($current / $target) * 100)) : 0;
                return [
                    'achievement' => $a,
                    'current' => $current,
                    'target' => $target,
                    'progress' => $progress,
                ];
            });

        return [
            'earned'  => $earnedRows,
            'locked'  => $locked,
            'points'  => (int) $donor->points,
            'level'   => (int) $donor->level,
        ];
    }
}
