<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class BloodTypeDemandWidget extends Widget
{
    protected string $view = 'filament.widgets.blood-type-demand-widget';

    protected int | string | array $columnSpan = 1;

    public function getDemandData(): array
    {
        return [
            'most_needed' => 'O+',
            'breakdown' => [
                ['label' => 'A+', 'value' => '23%'],
                ['label' => 'B+', 'value' => '18%'],
                ['label' => 'AB+', 'value' => '9%'],
                ['label' => 'أخرى', 'value' => '15%'],
            ],
        ];
    }
}
