<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class DonorIneligibilityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $eligibilityStatus;
    protected ?string $rejectionReason;
    protected mixed $nextEligibleDate;
    protected ?string $organizationName;

    public function __construct(
        string $eligibilityStatus,
        ?string $rejectionReason,
        mixed $nextEligibleDate,
        ?string $organizationName,
    ) {
        $this->eligibilityStatus = $eligibilityStatus;
        $this->rejectionReason   = $rejectionReason;
        $this->nextEligibleDate  = $nextEligibleDate;
        $this->organizationName  = $organizationName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $orgName = $this->organizationName ?? 'المنظمة';

        $rejectionLabels = [
            // Temporary
            'low_hemoglobin'    => 'نقص الهيموجلوبين',
            'underweight'       => 'نقص الوزن',
            'recent_illness'    => 'مرض حديث / مضادات حيوية',
            'low_blood_pressure' => 'انخفاض ضغط الدم',
            'other_temp'        => 'أسباب طبية مؤقتة أخرى',
            // Permanent
            'blood_virus'       => 'وجود فيروسات في الدم (HCV/HBV/HIV)',
            'chronic_disease'   => 'مرض مزمن يمنع التبرع',
            'heart_disease'     => 'أمراض القلب',
            'cancer'            => 'تاريخ مرضي للسرطان',
            'other_perm'        => 'أسباب طبية دائمة أخرى',
        ];

        $reasonLabel = $this->rejectionReason
            ? ($rejectionLabels[$this->rejectionReason] ?? $this->rejectionReason)
            : null;

        if ($this->eligibilityStatus === 'temporary') {
            $title = 'غير مؤهل مؤقتاً للتبرع';

            $body = "أفادت {$orgName} بأنك غير مؤهل للتبرع بالدم مؤقتاً";

            if ($reasonLabel) {
                $body .= " بسبب: {$reasonLabel}";
            }

            if ($this->nextEligibleDate) {
                $date = Carbon::parse($this->nextEligibleDate)->format('Y/m/d');
                $body .= ". موعد أهليتك المتوقع: {$date}";
            }

            return FilamentNotification::make()
                ->title($title)
                ->body($body)
                ->icon('heroicon-o-clock')
                ->iconColor('warning')
                ->getDatabaseMessage();
        }

        // Permanent
        $title = 'تم استبعادك من التبرع بالدم';

        $body = "أفادت {$orgName} باستبعادك الدائم من التبرع بالدم";

        if ($reasonLabel) {
            $body .= " بسبب: {$reasonLabel}";
        }

        return FilamentNotification::make()
            ->title($title)
            ->body($body)
            ->icon('heroicon-o-x-circle')
            ->iconColor('danger')
            ->getDatabaseMessage();
    }
}
