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

        // احتياط: لو المستخدم ليس لديه donor
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

        // 1) Total Donations
        $totalDonations = (int) ($profile?->total_donations ?? 0);

        // 2) Last Donation Date
        $lastDonation = $profile?->last_donation_date;
        $lastDonationLabel = $lastDonation ? $lastDonation->toDateString() : '—';

        // 3) Requests Received = طلبات أُرسلت للمتبرع فعلياً (صفوف request_responses)
        $requestsReceived = RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->count();

        // 4) Requests Accepted / Declined / Completed (من جدول request_responses للمتبرع)
        $responsesQuery = RequestResponse::query()->where('donor_id', $donor->id);

        // وافق = PENDING (الحالة الأولى بعد الإرسال تعني الموافقة من المتبرع)
        $accepted = (int) $responsesQuery->clone()
            ->where('status', RequestResponseStatus::PENDING)
            ->count();

        // رفض / اعتذر / لم يحضر / غير متاح
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

        // 5) Acceptance Rate = وافق / (وافق + رفض + اعتذار + لم يحضر + غير متاح)
        $responded = $accepted + $declined + $completed;
        $acceptanceRate = $responded > 0 ? round((($accepted + $completed) / $responded) * 100) : 0;

        // 6) Completion Rate = تم التبرع بنجاح / إجمالي الطلبات المستلمة
        $completionRate = $requestsReceived > 0 ? round(($completed / $requestsReceived) * 100) : 0;

        return [
            Stat::make('إجمالي التبرعات', $totalDonations)
                ->icon('heroicon-m-heart')
                ->color('danger') // 🔴 لون الدم / التبرع
                ->description('عدد تبرعاتك منذ التسجيل'),

            Stat::make('آخر تاريخ تبرع', $lastDonationLabel)
                ->icon('heroicon-m-calendar-days')
                ->color('info') // 🔵 معلومة زمنية
                ->description('أحدث تبرع قمت به'),

            Stat::make('الطلبات المستلمة', $requestsReceived)
                ->icon('heroicon-m-inbox')
                ->color('primary') // 🟣/🔵 عنصر أساسي (طلبات)
                ->description('طلبات مطابقة لفصيلة دمك'),

            Stat::make('الطلبات المقبولة', $accepted + $completed)
                ->icon('heroicon-m-check-badge')
                ->color('success') // 🟢 قبول
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
