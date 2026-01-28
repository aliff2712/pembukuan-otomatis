<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RawBeatImport;

$normalize = fn ($v) =>
    strtolower(trim(preg_replace('/\s+/', ' ', (string) $v)));

$rows = RawBeatImport::limit(3)->get();
$headers = array_map($normalize, $rows->first()->raw_payload);

$map = collect([
    'ppoe'      => 'pppoe',
    'admin by'  => 'admin_by',
])
    ->mapWithKeys(fn ($v, $k) => [$normalize($k) => $v])
    ->toArray();

echo "Mapping:\n";
print_r($map);
echo "\n";

foreach ($rows->skip(1) as $raw) {
    echo "Processing raw_id: {$raw->id}\n";
    
    $payload = $raw->raw_payload;
    
    $assoc = [];
    foreach ($headers as $i => $key) {
        $assoc[$key] = $payload[$i] ?? null;
    }
    
    echo "  ppoe in assoc: " . isset($assoc['ppoe']) . " = " . ($assoc['ppoe'] ?? 'NULL') . "\n";
    echo "  admin by in assoc: " . isset($assoc['admin by']) . " = " . ($assoc['admin by'] ?? 'NULL') . "\n";
    
    $staging = ['pppoe' => null, 'admin_by' => null];
    
    foreach ($map as $excelKey => $dbKey) {
        $value = $assoc[$excelKey] ?? null;
        $value = is_string($value) ? trim($value) : $value;
        
        $staging[$dbKey] = ($value !== '' && $value !== null) ? $value : null;
        
        echo "  Map: $excelKey => $dbKey = " . ($staging[$dbKey] ?? 'NULL') . "\n";
    }
    
    echo "  Final: pppoe=" . ($staging['pppoe'] ?? 'NULL') . " | admin_by=" . ($staging['admin_by'] ?? 'NULL') . "\n\n";
}
