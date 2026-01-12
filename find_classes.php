<?php
require 'vendor/autoload.php';

// Force load some classes to trigger autoloader if possible, but verifying declared classes is hard if they aren't loaded.
// Instead, let's look at the filesystem structure of vendor/filament.
// We can't see the filesystem?
// We can use the Composer ClassMapGenerator?
// Or just try to instantiate the Autoloader and inspect it.

// Better approach:
// Use `composer/class-map-generator` if available? No.
// Let's just try to check a few common locations.

$candidates = [
    'Filament\Schemas\Components\TextInput',
    'Filament\Schemas\Fields\TextInput',
    'Filament\Schemas\Inputs\TextInput',
    'Filament\Form\Components\TextInput', // maybe only Schema changed?
    'Filament\Forms\Components\TextInput', // checked, missing
];

foreach ($candidates as $class) {
    echo "$class: " . (class_exists($class) ? 'exists' : 'missing') . PHP_EOL;
}

// Also let's try to find where Section is defined and look around it.
if (class_exists('Filament\Schemas\Components\Section')) {
    $ref = new ReflectionClass('Filament\Schemas\Components\Section');
    echo "Section defined in: " . $ref->getFileName() . PHP_EOL;
    $dir = dirname($ref->getFileName());
    echo "Directory listing of $dir:" . PHP_EOL;
    foreach (scandir($dir) as $file) {
        echo " - $file" . PHP_EOL;
    }
} else {
    echo "Section class not found (weird)." . PHP_EOL;
}
