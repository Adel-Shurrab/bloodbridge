<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrganizationStatus: int implements HasLabel, HasColor
{
    case PENDING = 0;
    case APPROVED = 1;
    case REJECTED = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::APPROVED => __('Approved'),
            self::REJECTED => __('Rejected'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
