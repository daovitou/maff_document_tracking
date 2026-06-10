<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:check-dialy-expired-document')->dailyAt('22:00');
Schedule::command('app:check-dialy-expired-be-document')->dailyAt('21:50');
        