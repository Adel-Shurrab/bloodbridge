<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    // public function up(): void
    // {
    //     $this->migrator->add('general.enable_contact_messages', true);
    // }
    public function up(): void
    {
        // تحقق إذا كان الإعداد موجود مسبقًا قبل الإضافة
        if (! $this->migrator->exists('general.enable_contact_messages')) {
            $this->migrator->add('general.enable_contact_messages', true);
        }
    }
};
