<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RawBeatImport;

$normalize = fn ($v) =>
    strtolower(trim(preg_replace('/\s+/', ' ', (string) $v)));

$raw = RawBeatImport::find(1);
if ($raw) {
    $headers = array_map($normalize, $raw->raw_payload);
    echo "Normalized Headers:\n";
    foreach ($headers as $i => $h) {
        if (in_array($i, [4, 21])) {
            echo "Index $i: '$h'\n";
        }
    }
    
    $mapping = [
        'ppoe'      => 'pppoe',
        'admin by'  => 'admin_by',
    ];
    
    echo "\nMatching:\n";
    foreach ($mapping as $excelKey => $dbKey) {
        $found = in_array($excelKey, $headers);
        echo "$excelKey => found: " . ($found ? 'YES' : 'NO') . "\n";
    }
}
