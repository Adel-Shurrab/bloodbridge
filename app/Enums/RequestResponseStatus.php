<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RequestResponseStatus: int implements HasLabel, HasColor
{
    case PENDING = 0;
    case ACCEPTED = 1;
    case DECLINED = 2;
    case COMPLETED = 3;
    case IGNORED = 4;
    case NO_SHOW = 5;
    case UNREACHABLE = 6;
    case NOT_NEEDED = 7;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('Agree'),
            self::ACCEPTED => __('Attended'),
            self::DECLINED => __('Medical Exclusion'),
            self::COMPLETED => __('Donated Successfully'),
            self::IGNORED => __('Apologized'),
            self::NO_SHOW => __('Did Not Attend'),
            self::UNREACHABLE => __('Unreachable'),
            self::NOT_NEEDED => __('Not Needed'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ACCEPTED => 'success',
            self::DECLINED, self::IGNORED, self::NO_SHOW => 'danger',
            self::COMPLETED => 'success',
            self::UNREACHABLE, self::NOT_NEEDED => 'gray',
        };
    }
}
