<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BeatSubscriptionStaging;

$count = BeatSubscriptionStaging::count();
echo "Total staging records: $count\n\n";

$sample = BeatSubscriptionStaging::first();
if ($sample) {
    echo "Sample record:\n";
    print_r($sample->toArray());
}
