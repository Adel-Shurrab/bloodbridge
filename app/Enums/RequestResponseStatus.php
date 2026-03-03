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
            self::PENDING => 'وافق',
            self::ACCEPTED => 'حضر',
            self::DECLINED => 'استبعاد طبي',
            self::COMPLETED => 'تم التبرع بنجاح',
            self::IGNORED => 'معتذر',
            self::NO_SHOW => 'لم يحضر',
            self::UNREACHABLE => 'غير متاح',
            self::NOT_NEEDED => 'لم يعد مطلوباً',
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
