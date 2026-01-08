<?php

namespace App\Filament\Widgets;

class AdvancedStatsOverview extends StatsOverview
{
    protected function getStats(): array
    {
        return [
            $this->makeStat(
                label: 'الطلبات المكتملة',
                value: '1,234',
                icon: 'heroicon-s-check-circle',
                theme: 'emerald',
            ),
            $this->makeStat(
                label: 'الطلبات المرسلة',
                value: '2,567',
                icon: 'heroicon-s-play-circle',
                theme: 'blue',
            ),
            $this->makeStat(
                label: 'نسبة القبول',
                value: '48.07%',
                icon: 'heroicon-s-variable',
                theme: 'orange',
            ),
            $this->makeStat(
                label: 'إجمالي المتبرعين',
                value: '5,890',
                icon: 'heroicon-s-users',
                theme: 'indigo',
            ),
        ];
    }
}
