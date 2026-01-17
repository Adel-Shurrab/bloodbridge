<?php
try {
    $reflector = new ReflectionFunction('update');
    echo "Function 'update' found!\n";
    echo "File: " . $reflector->getFileName() . "\n";
    echo "Line: " . $reflector->getStartLine() . "\n";
} catch (ReflectionException $e) {
    echo "Function 'update' not found as a global function.\n";
}
