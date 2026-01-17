<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BloodType: int implements HasLabel
{
    case O_POSITIVE = 1;
    case O_NEGATIVE = 2;
    case A_POSITIVE = 3;
    case A_NEGATIVE = 4;
    case B_POSITIVE = 5;
    case B_NEGATIVE = 6;
    case AB_POSITIVE = 7;
    case AB_NEGATIVE = 8;

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
        };
    }
}
