<?php

namespace App\Filament\Organization\Widgets;

use App\Enums\BloodRequestStatus;
use App\Models\Organization;
use App\Models\BloodRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class BloodRequestStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $organization = $this->getOrganization();

        if (! $organization) {
            return [];
        }

        $activeRequests = BloodRequest::where('organization_id', $organization->id)
            ->whereIn('status', [
                BloodRequestStatus::PENDING,
                BloodRequestStatus::BROADCASTED,
            ])
            ->count();

        $criticalRequests = BloodRequest::where('organization_id', $organization->id)
            ->whereIn('status', [
                BloodRequestStatus::PENDING,
                BloodRequestStatus::BROADCASTED,
            ])
            ->where('urgency_level', \App\Enums\UrgencyLevel::CRITICAL)
            ->count();

        $pendingResponses = \App\Models\RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->where('status', \App\Enums\RequestResponseStatus::PENDING)
            ->count();

        $todayDonations = \App\Models\RequestResponse::whereHas('bloodRequest', function ($query) use ($organization) {
            $query->where('organization_id', $organization->id);
        })
            ->where('status', \App\Enums\RequestResponseStatus::COMPLETED)
            ->whereDate('updated_at', today())
            ->count();

        return [
            Stat::make(__('organization.active_blood_requests'), $activeRequests)
                ->description(__('organization.pending_and_activated_requests'))
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color($activeRequests > 0 ? 'warning' : 'gray')
                ->url(route('filament.organization.resources.blood-requests.index', ['tenant' => $organization->slug])),

            Stat::make(__('organization.urgent_requests'), $criticalRequests)
                ->description(__('organization.needs_immediate_attention'))
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($criticalRequests > 0 ? 'danger' : 'success'),

            Stat::make(__('organization.donor_responses'), $pendingResponses)
                ->description(__('organization.awaiting_review'))
                ->descriptionIcon('heroicon-o-user-group')
                ->color($pendingResponses > 0 ? 'info' : 'gray'),

            Stat::make(__('organization.todays_donations'), $todayDonations)
                ->description(__('organization.completed_today'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }

    protected function getOrganization(): ?Organization
    {
        $tenant = filament()->getTenant();

        if ($tenant instanceof Organization) {
            return $tenant;
        }

        return Auth::user()?->organization;
    }
}
