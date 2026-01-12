<?php
require __DIR__ . '/vendor/autoload.php';

try {
    $class = new ReflectionClass('Filament\Pages\SettingsPage');
    $method = $class->getMethod('form');
    echo "Method: " . $method->getName() . "\n";
    echo "Is Static: " . ($method->isStatic() ? 'Yes' : 'No') . "\n";
    foreach ($method->getParameters() as $param) {
        echo "Param: " . $param->getName() . " Type: " . $param->getType() . "\n";
    }
    echo "Return Type: " . $method->getReturnType() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
