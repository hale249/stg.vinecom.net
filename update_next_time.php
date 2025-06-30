<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find the investment with ID 2
$invest = \App\Models\Invest::find(2);

if ($invest) {
    $invest->next_time = '2025-07-30 22:07:00';
    $invest->save();
    echo "Investment ID " . $invest->id . " updated successfully.\n";
    echo "Next time set to: " . $invest->next_time . "\n";
} else {
    echo "Investment with ID 2 not found.\n";
} 