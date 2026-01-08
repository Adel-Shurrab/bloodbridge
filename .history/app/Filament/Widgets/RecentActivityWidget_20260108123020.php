<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\User;

class RecentActivityWidget extends Widget
{
    protected string $view = 'filament.widgets.recent-activity-widget';

    public function getActivities(): array
    {
        $activities = collect();
        $bloodTypeOptions = \App\Models\Donor::getBloodTypeOptions();

        // Latest Blood Requests
        BloodRequest::latest()->take(5)->get()->each(function ($request) use ($activities, $bloodTypeOptions) {
            $bloodLabel = $bloodTypeOptions[$request->blood_type] ?? $request->blood_type;
            $activities->push([
                'title' => "تم إنشاء طلب تبرع جديد لـ {$bloodLabel}.",
                'time' => $request->created_at->diffForHumans(),
                'icon' => 'heroicon-m-plus-circle',
                'color' => 'blue',
                'timestamp' => $request->created_at,
            ]);
        });

        // Latest Donors
        \App\Models\Donor::latest()->take(5)->get()->each(function ($donor) use ($activities) {
            $activities->push([
                'title' => "متبرع جديد ({$donor->user->name}) مسجل في النظام.",
                'time' => $donor->created_at->diffForHumans(),
                'icon' => 'heroicon-m-user-plus',
                'color' => 'emerald',
                'timestamp' => $donor->created_at,
            ]);
        });

        // Latest Organizations Approved
        \App\Models\Organization::where('approval_status', \App\Models\Organization::STATUS_APPROVED)
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->each(function ($org) use ($activities) {
                $activities->push([
                    'title' => "تمت الموافقة على اعتماد مؤسسة: {$org->name}.",
                    'time' => $org->updated_at->diffForHumans(),
                    'icon' => 'heroicon-m-check-badge',
                    'color' => 'orange',
                    'timestamp' => $org->updated_at,
                ]);
            });

        return $activities->sortByDesc('timestamp')->take(5)->toArray();
    }
}
