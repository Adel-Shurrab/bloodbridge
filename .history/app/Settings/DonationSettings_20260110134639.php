<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DonationSettings extends Settings
{
    public float $min_weight;
    public float $min_height;
    public int $donation_deferral_days;
    public int $surgery_deferral_days;
    public int $infection_deferral_days;

    public static function group(): string
    {
        return 'donation';
    }
}
