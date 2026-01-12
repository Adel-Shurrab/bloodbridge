<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = app(App\Settings\GeneralSettings::class);
$settings->site_active = false; // Set back to active
$settings->maintenance_message = 'الموقع حالياً في وضع الصيانة، سنعود قريباً.';
$settings->save();

echo "Maintenance Mode REVERTED to: " . ($settings->site_active ? 'ENABLED' : 'DISABLED') . "\n";
