<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: int implements HasLabel, HasColor
{
    case ADMIN = 3;
    case DONOR = 1;
    case ORGANIZATION = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ADMIN => __('System Administrator'),
            self::DONOR => __('Donor'),
            self::ORGANIZATION => __('Organization'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ADMIN => 'danger',
            self::DONOR => 'success',
            self::ORGANIZATION => 'info',
        };
    }
}
