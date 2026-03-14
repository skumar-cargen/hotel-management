<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Execute scheduled price updates every minute
Schedule::command('pricing:execute-scheduled')->everyMinute();

// Aggregate analytics data daily at midnight
Schedule::command('analytics:aggregate')->dailyAt('23:55');
