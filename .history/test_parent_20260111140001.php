<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$r = new ReflectionClass('App\Filament\Pages\ManageGeneralSettings');
echo "Parent: " . $r->getParentClass()->getName() . "\n";
echo "File: " . $r->getParentClass()->getFileName() . "\n";
