<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UrgencyLevel: int implements HasLabel, HasColor
{
    case NORMAL = 1;
    case CRITICAL = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NORMAL => __('Normal'),
            self::CRITICAL => __('Critical'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NORMAL => 'info',
            self::CRITICAL => 'danger',
        };
    }
}
