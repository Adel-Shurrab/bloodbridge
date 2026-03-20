<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BloodRequestStatus: int implements HasLabel, HasColor
{
    case PENDING = 0;
    case BROADCASTED = 1;
    case FULFILLED = 3;
    case CANCELLED = 4;
    case EXPIRED = 5;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::BROADCASTED => __('Broadcasted'),
            self::FULFILLED => __('Fulfilled'),
            self::CANCELLED => __('Cancelled'),
            self::EXPIRED => __('Expired'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::BROADCASTED => 'info',
            self::FULFILLED => 'success',
            self::CANCELLED, self::EXPIRED => 'danger',
        };
    }
}
