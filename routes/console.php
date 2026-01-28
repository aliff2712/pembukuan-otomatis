<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

app(Schedule::class)->command('mikhmon:import')->dailyAt('13:35');

app(Schedule::class)->command('mikhmon:transform')->dailyAt('13:40');

app(Schedule::class)->command('mikhmon:aggregate-daily')->dailyAt('13:45');