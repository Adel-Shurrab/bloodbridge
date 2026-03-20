<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\User;
use App\Models\Organization;

class RecentActivityWidget extends Widget
{
    protected string $view = 'filament.widgets.recent-activity-widget';

    protected int | string | array $columnSpan = 1;

    public function getActivities(): array
    {
        $activities = collect();

        BloodRequest::latest()->limit(5)->get()->each(function ($request) use ($activities) {
            $bloodLabel = $request->blood_type?->getLabel() ?? $request->blood_type;
            $activities->push([
                'title' => __('New donation request created for :type.', ['type' => $bloodLabel]),
                'time' => $request->created_at->diffForHumans(),
                'icon' => 'heroicon-m-plus-circle',
                'color' => 'blue',
                'timestamp' => $request->created_at,
            ]);
        });

        Donor::with('user')->latest()->limit(5)->get()->each(function ($donor) use ($activities) {
            $activities->push([
                'title' => __('New donor (:name) registered in the system.', ['name' => $donor->user->name ?? 'Unknown']),
                'time' => $donor->created_at->diffForHumans(),
                'icon' => 'heroicon-m-user-plus',
                'color' => 'emerald',
                'timestamp' => $donor->created_at,
            ]);
        });

        Organization::where('approval_status', \App\Enums\OrganizationStatus::APPROVED)
            ->latest('updated_at')
            ->limit(5)
            ->get()
            ->each(function ($org) use ($activities) {
                $activities->push([
                    'title' => __('Organization approval successful: :name.', ['name' => $org->org_name ?? $org->name]),
                    'time' => $org->updated_at->diffForHumans(),
                    'icon' => 'heroicon-m-check-badge',
                    'color' => 'orange',
                    'timestamp' => $org->updated_at,
                ]);
            });

        return $activities->sortByDesc('timestamp')->take(5)->values()->toArray();
    }
}
