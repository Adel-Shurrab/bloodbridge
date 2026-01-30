<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BloodType: int implements HasLabel, HasColor
{
    case O_POSITIVE = 1;
    case O_NEGATIVE = 2;
    case A_POSITIVE = 3;
    case A_NEGATIVE = 4;
    case B_POSITIVE = 5;
    case B_NEGATIVE = 6;
    case AB_POSITIVE = 7;
    case AB_NEGATIVE = 8;
    case UNKNOWN = 9;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::O_POSITIVE => 'O+',
            self::O_NEGATIVE => 'O-',
            self::A_POSITIVE => 'A+',
            self::A_NEGATIVE => 'A-',
            self::B_POSITIVE => 'B+',
            self::B_NEGATIVE => 'B-',
            self::AB_POSITIVE => 'AB+',
            self::AB_NEGATIVE => 'AB-',
            self::UNKNOWN => 'غير معروف',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UNKNOWN => 'gray',
            default => 'danger',
        };
    }

    /**
     * Get compatible donor blood types that can donate to this blood type
     * 
     * @return array<self>
     */
    public function getCompatibleDonorTypes(): array
    {
        return match ($this) {
            self::O_POSITIVE => [
                self::O_POSITIVE,
                self::O_NEGATIVE,
            ],
            self::O_NEGATIVE => [
                self::O_NEGATIVE,
            ],
            self::A_POSITIVE => [
                self::A_POSITIVE,
                self::A_NEGATIVE,
                self::O_POSITIVE,
                self::O_NEGATIVE,
            ],
            self::A_NEGATIVE => [
                self::A_NEGATIVE,
                self::O_NEGATIVE,
            ],
            self::B_POSITIVE => [
                self::B_POSITIVE,
                self::B_NEGATIVE,
                self::O_POSITIVE,
                self::O_NEGATIVE,
            ],
            self::B_NEGATIVE => [
                self::B_NEGATIVE,
                self::O_NEGATIVE,
            ],
            self::AB_POSITIVE => [
                self::AB_POSITIVE,
                self::AB_NEGATIVE,
                self::A_POSITIVE,
                self::A_NEGATIVE,
                self::B_POSITIVE,
                self::B_NEGATIVE,
                self::O_POSITIVE,
                self::O_NEGATIVE,
            ],
            self::AB_NEGATIVE => [
                self::AB_NEGATIVE,
                self::A_NEGATIVE,
                self::B_NEGATIVE,
                self::O_NEGATIVE,
            ],
        };
    }
}
