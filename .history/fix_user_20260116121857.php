<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
if ($user) {
    // Assuming organization ID 1 exists as checked before
    $user->organization_id = 1;
    $user->save();
    echo "Successfully updated User ID {$user->id} with Organization ID 1.\n";
    echo "Check: " . $user->organization->name . "\n";
} else {
    echo "No user found.\n";
}
