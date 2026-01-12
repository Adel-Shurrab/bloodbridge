<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::table('settings')->get();
foreach ($rows as $row) {
    echo $row->group . "." . $row->name . " = " . $row->payload . "\n";
}
