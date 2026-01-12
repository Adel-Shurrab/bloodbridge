<?php
require __DIR__ . '/vendor/autoload.php';

try {
    echo "Loading DonationSettings...\n";
    $class = new ReflectionClass('App\Settings\DonationSettings');
    echo "DonationSettings loaded.\n";

    echo "Loading ManageDonationSettings...\n";
    $class = new ReflectionClass('App\Filament\Pages\ManageDonationSettings');
    echo "ManageDonationSettings loaded.\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
