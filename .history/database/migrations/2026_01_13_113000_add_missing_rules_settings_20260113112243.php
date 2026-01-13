<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Check if exists to avoid duplication error if run multiple times
        if (! $this->migrator->exists('general.min_donor_age')) {
            $this->migrator->add('general.min_donor_age', 18);
        }
        if (! $this->migrator->exists('general.max_donor_age')) {
            $this->migrator->add('general.max_donor_age', 65);
        }
        if (! $this->migrator->exists('general.min_donor_weight')) {
            $this->migrator->add('general.min_donor_weight', 50);
        }
        if (! $this->migrator->exists('general.min_days_between_donations')) {
            $this->migrator->add('general.min_days_between_donations', 56);
        }
        if (! $this->migrator->exists('general.org_max_requests_per_day')) {
            $this->migrator->add('general.org_max_requests_per_day', 5);
        }
    }
};
