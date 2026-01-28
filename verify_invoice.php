<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BeatInvoice;

$count = BeatInvoice::count();
echo "Total invoices generated: $count\n\n";

$sample = BeatInvoice::first();
if ($sample) {
    echo "Sample invoice:\n";
    print_r($sample->toArray());
}
