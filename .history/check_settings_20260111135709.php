<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = app(App\Settings\GeneralSettings::class);
echo "Name: " . $settings->site_name . "\n";
echo "Slogan: " . $settings->site_slogan . "\n";
echo "Hero Title: " . $settings->home_hero_title . "\n";
