<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.min_donor_age', 18);
        $this->migrator->add('general.max_donor_age', 65);
        $this->migrator->add('general.min_donor_weight', 50);
        $this->migrator->add('general.min_days_between_donations', 56);
        $this->migrator->add('general.org_max_requests_per_day', 5);
    }
};
