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

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'قيد الانتظار',
            self::ACCEPTED => 'قادم للتبرع',
            self::DECLINED => 'استبعاد طبي',
            self::COMPLETED => 'تم التبرع بنجاح',
            self::IGNORED => 'متجاهل',
            self::NO_SHOW => 'لم يحضر',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::ACCEPTED => 'info',
            self::DECLINED, self::IGNORED, self::NO_SHOW => 'danger',
            self::COMPLETED => 'success',
        };
    }
}
