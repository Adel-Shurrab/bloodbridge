<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$org = \App\Models\Organization::where('user_id', 1)->first();
if ($org) {
    echo "User 1 already manages Organization: {$org->name} (ID: {$org->id})\n";
} else {
    echo "User 1 manages no organization. Checking for available orgs...\n";
    $firstOrg = \App\Models\Organization::first();
    if ($firstOrg) {
        $firstOrg->user_id = 1;
        $firstOrg->save();
        echo "Assigned Organization {$firstOrg->name} (ID: {$firstOrg->id}) to User 1.\n";
    } else {
        echo "No organizations found to assign.\n";
    }
}
