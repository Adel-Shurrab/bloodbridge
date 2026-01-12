<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = app(App\Settings\GeneralSettings::class);
$settings->home_hero_title = 'مرحباً بك في موقعنا';
$settings->save();
echo "Reset to: " . $settings->home_hero_title . "\n";
