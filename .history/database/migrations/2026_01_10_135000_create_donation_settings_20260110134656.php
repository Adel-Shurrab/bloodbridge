<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('donation.min_weight', 50.0);
        $this->migrator->add('donation.min_height', 140.0);
        $this->migrator->add('donation.donation_deferral_days', 90);
        $this->migrator->add('donation.surgery_deferral_days', 28);
        $this->migrator->add('donation.infection_deferral_days', 14);
    }
};
