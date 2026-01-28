<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$data = DB::table('beat_subscription_stagings')->limit(5)->get();
foreach ($data as $r) {
    echo "ID: {$r->id} | pppoe: {$r->pppoe} | admin_by: {$r->admin_by}\n";
}
