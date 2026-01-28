<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RawBeatImport;

$raw = RawBeatImport::find(2);
if ($raw) {
    echo "Raw ID 2 Payload (first data row):\n";
    print_r($raw->raw_payload);
    echo "\nPayload length: " . count($raw->raw_payload) . "\n";
    echo "\nSpecific columns:\n";
    echo "Index 4 (PPOE): " . ($raw->raw_payload[4] ?? 'MISSING') . "\n";
    echo "Index 21 (Admin By): " . ($raw->raw_payload[21] ?? 'MISSING') . "\n";
} else {
    echo "No raw beat import ID 2 found\n";
}
