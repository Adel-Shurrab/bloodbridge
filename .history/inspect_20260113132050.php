<?php

require __DIR__ . '/vendor/autoload.php';

use Filament\Schemas\Schema;
use ReflectionClass;

$class = new ReflectionClass(Schema::class);
if ($class->hasMethod('schema')) {
    echo "Has schema method.\n";
} else {
    echo "Does NOT have schema method.\n";
}
if ($class->hasMethod('components')) {
    echo "Has components method.\n";
}
