<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatically purge captured credentials and click logs older than the
// default retention period (90 days) once a day. This still requires the
// server's cron to call `php artisan schedule:run` every minute - see
// Laravel's task scheduling docs for production setup.
Schedule::command('phishing:purge')->daily();
