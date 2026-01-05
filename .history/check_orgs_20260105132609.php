<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orgs = \App\Models\Organization::all(['id', 'org_name', 'responsible_person_name', 'responsible_person_email']);
echo "Total Orgs: " . $orgs->count() . "\n";
foreach ($orgs as $org) {
    echo "ID: {$org->id} | Name: {$org->org_name} | Resp Name: '{$org->responsible_person_name}' | Resp Email: '{$org->responsible_person_email}'\n";
}
