<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppointmentStatus: int implements HasLabel, HasColor
{
    case SCHEDULED = 0;
    case COMPLETED = 1;
    case CANCELLED = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SCHEDULED => 'مجدول',
            self::COMPLETED => 'مكتمل',
            self::CANCELLED => 'ملغي',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SCHEDULED => 'info',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
