<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$count = DB::table('beat_invoices')->count();
echo "Total invoices created: $count\n";

$stagingCount = DB::table('beat_subscription_stagings')->count();
$invoicedCount = DB::table('beat_subscription_stagings')->whereHas('invoice')->count();
echo "Total staging: $stagingCount\n";
echo "Invoiced staging: $invoicedCount\n";
