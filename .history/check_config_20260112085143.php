<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Cache Driver: " . config('cache.default') . "\n";
echo "Session Driver: " . config('session.driver') . "\n";
echo "App Env: " . config('app.env') . "\n";
echo "App Debug: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "Settings Cache: " . (config('settings.cache.enabled') ? 'true' : 'false') . "\n";
