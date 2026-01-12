<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = app(App\Settings\GeneralSettings::class);
$settings->site_active = true;
$settings->maintenance_message = 'TEST MAINTENANCE MESSAGE';
$settings->save();

echo "Maintenance Mode: " . ($settings->site_active ? 'ENABLED' : 'DISABLED') . "\n";
echo "Message: " . $settings->maintenance_message . "\n";
