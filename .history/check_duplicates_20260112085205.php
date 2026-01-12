<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$duplicates = DB::table('settings')
    ->select('group', 'name', DB::raw('COUNT(*) as count'))
    ->groupBy('group', 'name')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "No duplicates found.\n";
} else {
    foreach ($duplicates as $dup) {
        echo "Duplicate: " . $dup->group . "." . $dup->name . " (" . $dup->count . ")\n";
    }
}
