<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$r = new ReflectionClass('Filament\Pages\SettingsPage');
foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
    if ($m->class == 'Filament\Pages\SettingsPage') {
        echo $m->getName() . "(";
        foreach ($m->getParameters() as $p) {
            echo $p->getType() . " $" . $p->getName() . ", ";
        }
        echo ")\n";
    }
}
