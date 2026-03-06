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
                Stat::make('Total Donations', '—'),
                Stat::make('Last Donation Date', '—'),
                Stat::make('Requests Received', '—'),
                Stat::make('Requests Accepted', '—'),
                Stat::make('Acceptance Rate', '—'),
                Stat::make('Completion Rate', '—'),
            ];
        }

        $profile = $donor->healthProfile;

        $totalDonations = (int) ($profile?->total_donations ?? 0);

        $lastDonation = $profile?->last_donation_date;
        $lastDonationLabel = $lastDonation ? $lastDonation->toDateString() : '—';

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
            Stat::make('إجمالي التبرعات', $totalDonations)
                ->icon('heroicon-m-heart')
                ->color('danger') 
                ->description('عدد تبرعاتك منذ التسجيل'),

            Stat::make('آخر تاريخ تبرع', $lastDonationLabel)
                ->icon('heroicon-m-calendar-days')
                ->color('info') 
                ->description('أحدث تبرع قمت به'),

            Stat::make('الطلبات المستلمة', $requestsReceived)
                ->icon('heroicon-m-inbox')
                ->color('primary') 
                ->description('طلبات مطابقة لفصيلة دمك'),

            Stat::make('الطلبات المقبولة', $accepted + $completed)
                ->icon('heroicon-m-check-badge')
                ->color('success') 
                ->description('عدد الطلبات التي وافقت عليها'),

            Stat::make('نسبة القبول', $acceptanceRate . '%')
                ->icon('heroicon-m-chart-bar')
                ->color($acceptanceRate >= 70 ? 'success' : 'warning')
                ->description('المقبول من إجمالي ردودك'),

            Stat::make('نسبة الإكمال', $completionRate . '%')
                ->icon('heroicon-m-trophy')
                ->color($completionRate === 100 ? 'success' : 'warning')
                ->description('المكتمل من الطلبات المقبولة'),
        ];
    }
}
