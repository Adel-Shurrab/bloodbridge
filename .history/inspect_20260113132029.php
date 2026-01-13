<?php

require __DIR__ . '/vendor/autoload.php';

use Filament\Resources\RelationManagers\RelationManager;
use ReflectionMethod;

$method = new ReflectionMethod(RelationManager::class, 'form');
echo "Parameters:\n";
foreach ($method->getParameters() as $parameter) {
    echo "- " . $parameter->getName();
    if ($parameter->hasType()) {
        echo ": " . $parameter->getType()->getName();
    }
    echo "\n";
}
echo "Return Type:\n";
if ($method->hasReturnType()) {
    echo $method->getReturnType()->getName();
}
echo "\n";
