<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\User;

class RecentActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.recent-activity-widget';

    protected int | string | array $columnSpan = 1;

    public function getActivities(): array
    {
        // For demonstration, combining some recent records. 
        // In a real app, you might have an 'Activity' log table.
        return [
            [
                'type' => 'request',
                'title' => 'تم إنشاء طلب تبرع جديد لـ A+ blood.',
                'time' => 'منذ دقيقتين',
                'icon' => 'heroicon-m-plus-circle',
                'color' => 'blue',
            ],
            [
                'type' => 'approval',
                'title' => 'تمت الموافقة على التحقق من المتبرع John Doe.',
                'time' => 'منذ 15 دقيقة',
                'icon' => 'heroicon-m-check-circle',
                'color' => 'emerald',
            ],
            [
                'type' => 'donor',
                'title' => 'متبرع جديد Jane Smith مسجل، في انتظار التحقق.',
                'time' => 'منذ ساعة واحدة',
                'icon' => 'heroicon-m-user',
                'color' => 'orange',
            ],
            [
                'type' => 'cancellation',
                'title' => 'تم إلغاء طلب التبرع لـ B-.',
                'time' => 'منذ 3 ساعات',
                'icon' => 'heroicon-m-x-circle',
                'color' => 'red',
            ],
            [
                'type' => 'campaign',
                'title' => 'تم إطلاق حملة جديدة "Summer Drive".',
                'time' => 'منذ 5 ساعات',
                'icon' => 'heroicon-m-megaphone',
                'color' => 'pink',
            ],
        ];
    }
}
