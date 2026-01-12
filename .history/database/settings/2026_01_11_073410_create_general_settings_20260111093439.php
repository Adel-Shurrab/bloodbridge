<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'BloodBridge');
        $this->migrator->add('general.site_active', true);
        $this->migrator->add('general.main_content', 'Welcome to the system.');
        $this->migrator->add('general.support_email', 'admin@bloodbridge.com');
    }
};
