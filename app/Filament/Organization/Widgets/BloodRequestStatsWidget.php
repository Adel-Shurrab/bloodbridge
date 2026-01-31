<?php

namespace App\Filament\Organization\Widgets;

use App\Enums\BloodRequestStatus;
use App\Models\BloodRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class BloodRequestStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $organization = Auth::user()->organization;

        // Active Requests (PENDING, BROADCASTED)
        $activeRequests = BloodRequest::where('organization_id', $organization->id)
            ->whereIn('status', [
                BloodRequestStatus::PENDING,
                BloodRequestStatus::BROADCASTED,
            ])
            ->count();

        // Critical Requests
        $criticalRequests = BloodRequest::where('organization_id', $organization->id)
            ->whereIn('status', [
                BloodRequestStatus::PENDING,
                BloodRequestStatus::BROADCASTED,
            ])
            ->where('urgency_level', \App\Enums\UrgencyLevel::CRITICAL)
            ->count();

        // Pending Responses (waiting for org action)
        $pendingResponses = \App\Models\RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->where('status', \App\Enums\RequestResponseStatus::PENDING)
            ->count();

        // Today's Completed Donations
        $todayDonations = \App\Models\RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->where('status', \App\Enums\RequestResponseStatus::COMPLETED)
            ->whereDate('updated_at', today())
            ->count();

        return [
            Stat::make('طلبات الدم النشطة', $activeRequests)
                ->description('الطلبات المعلقة والمفعلة')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color($activeRequests > 0 ? 'warning' : 'gray')
                ->url(route('filament.organization.resources.blood-requests.index', ['tenant' => $organization->slug])),

            Stat::make('🚨 طلبات عاجلة', $criticalRequests)
                ->description('تحتاج اهتمام فوري')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($criticalRequests > 0 ? 'danger' : 'success'),

            Stat::make('استجابات المتبرعين', $pendingResponses)
                ->description('بانتظار المراجعة')
                ->descriptionIcon('heroicon-o-user-group')
                ->color($pendingResponses > 0 ? 'info' : 'gray'),

            Stat::make('تبرعات اليوم', $todayDonations)
                ->description('تم إكمالها اليوم')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
