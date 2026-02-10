<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

/*
|--------------------------------------------------------------------------
| MIKHMON FLOW (Voucher Sales)
|--------------------------------------------------------------------------
*/
app(Schedule::class)->command('mikhmon:import')->dailyAt('11:08');

app(Schedule::class)->command('mikhmon:transform')->dailyAt('11:10');

app(Schedule::class)->command('mikhmon:aggregate-daily')->dailyAt('11:12');

app(Schedule::class)->command('mikhmon:journalize {date?}')->dailyAt('11:14');
/*
|--------------------------------------------------------------------------
| BEAT FLOW (ISP Subscriptions)
|--------------------------------------------------------------------------
| Step 1: Import raw Beat data
| Step 2: Transform and normalize staging data
| Step 3: Generate invoices from subscriptions
| Step 4: Record payments
| Step 5: Journalize invoices and payments
| Step 6: Post journals to ledger
*/

// Step 1: Import raw Beat CSV/XLSX data (scheduled daily at 14:00)
app(Schedule::class)->command('beat:import-raw')->dailyAt('11:16');

// Step 2: Transform raw Beat imports to staging (scheduled 10 minutes after import)
app(Schedule::class)->command('beat:transform-staging')->dailyAt('11:18');

// Step 3: Generate Beat invoices from staging subscriptions (scheduled 10 minutes after transform)
app(Schedule::class)->command('beat:generate-invoices')->dailyAt('11:20');

// Step 4: Record Beat payments (scheduled 10 minutes after invoice generation)
app(Schedule::class)->command('beat:record-payment')->dailyAt('11:22');

// Step 5a: Journalize Beat invoices to accounting entries (scheduled 10 minutes after payments)
app(Schedule::class)->command('journal:beat-invoice')->dailyAt('11:24');

// Step 5b: Journalize Beat payments to accounting entries (scheduled 5 minutes after invoices)
app(Schedule::class)->command('journal:beat-payment')->dailyAt('11:26');

// Step 6: Post Beat journals to ledger (scheduled 5 minutes after journalize)
app(Schedule::class)->command('beat:post-journals')->dailyAt('11:28');