<?php

use Illuminate\Support\Facades\Lang;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing helpers...\n";

// Test constants function
$roles = constants('user.roles');
echo "Roles: " . json_encode($roles) . "\n";
if (isset($roles['DONOR']) && $roles['DONOR'] === 'donor') {
    echo "✓ constants() works.\n";
} else {
    echo "✗ constants() failed.\n";
}

// Test trans_const with and without prefix
// We need to simulate the translations being loaded. 
// Since we are in a bootstrapped app, it should work if the files exist.

echo "Gender Male (no prefix): " . trans_const('gender.male') . "\n";
echo "Gender Male (with prefix): " . trans_const('constants.gender.male') . "\n";

if (trans_const('gender.male') === trans_const('constants.gender.male')) {
    echo "✓ trans_const() prefix handling works.\n";
} else {
    echo "✗ trans_const() prefix handling failed.\n";
}

echo "Done.\n";
