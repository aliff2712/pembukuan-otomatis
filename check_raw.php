<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RawBeatImport;

$raw = RawBeatImport::find(1);
if ($raw) {
    echo "Raw Payload (first row):\n";
    print_r($raw->raw_payload);
    echo "\nPayload length: " . count($raw->raw_payload) . "\n";
} else {
    echo "No raw beat import found\n";
}
