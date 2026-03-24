<?php

namespace App\Filament\Donor\Widgets;

use App\Enums\RequestResponseStatus;
use App\Models\RequestResponse;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DonorStatsOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $donor = $user?->donor;

        if (! $donor) {
            return [
                Stat::make(__('donor.total_donations'), '-'),
                Stat::make(__('donor.last_donation_date'), '-'),
                Stat::make(__('donor.requests_received'), '-'),
                Stat::make(__('donor.requests_accepted'), '-'),
                Stat::make(__('donor.acceptance_rate'), '-'),
                Stat::make(__('donor.completion_rate'), '-'),
            ];
        }

        $profile = $donor->healthProfile;

        $totalDonations = (int) ($profile?->total_donations ?? 0);

        $lastDonation = $profile?->last_donation_date;
        $lastDonationLabel = $lastDonation ? $lastDonation->toDateString() : '-';

        $requestsReceived = RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->count();

        $responsesQuery = RequestResponse::query()->where('donor_id', $donor->id);

        $accepted = (int) $responsesQuery->clone()
            ->where('status', RequestResponseStatus::PENDING)
            ->count();

        $declined = (int) $responsesQuery->clone()
            ->whereIn('status', [
                RequestResponseStatus::DECLINED,
                RequestResponseStatus::IGNORED,
                RequestResponseStatus::NO_SHOW,
                RequestResponseStatus::UNREACHABLE,
            ])
            ->count();

        $completed = (int) $responsesQuery->clone()
            ->where('status', RequestResponseStatus::COMPLETED)
            ->count();

        $responded = $accepted + $declined + $completed;
        $acceptanceRate = $responded > 0 ? round((($accepted + $completed) / $responded) * 100) : 0;

        $completionRate = $requestsReceived > 0 ? round(($completed / $requestsReceived) * 100) : 0;

        return [
            Stat::make(__('donor.total_donations'), $totalDonations)
                ->icon('heroicon-m-heart')
                ->color('danger')
                ->description(__('donor.donations_since_registration')),

            Stat::make(__('donor.last_donation_date'), $lastDonationLabel)
                ->icon('heroicon-m-calendar-days')
                ->color('info')
                ->description(__('donor.most_recent_donation')),

            Stat::make(__('donor.requests_received'), $requestsReceived)
                ->icon('heroicon-m-inbox')
                ->color('primary')
                ->description(__('donor.requests_matching_blood_type')),

            Stat::make(__('donor.requests_accepted'), $accepted + $completed)
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->description(__('donor.accepted_requests_count')),

            Stat::make(__('donor.acceptance_rate'), $acceptanceRate . '%')
                ->icon('heroicon-m-chart-bar')
                ->color($acceptanceRate >= 70 ? 'success' : 'warning')
                ->description(__('donor.accepted_out_of_total')),

            Stat::make(__('donor.completion_rate'), $completionRate . '%')
                ->icon('heroicon-m-trophy')
                ->color($completionRate === 100 ? 'success' : 'warning')
                ->description(__('donor.completed_out_of_accepted')),
        ];
    }
}
