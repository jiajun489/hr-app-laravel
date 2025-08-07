<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule overtime alerts to run every Monday at 9 AM
Schedule::command('worklife:check-overtime')->weeklyOn(1, '9:00');
