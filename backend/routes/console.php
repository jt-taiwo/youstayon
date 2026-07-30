<?php 

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Keep going');

Schedule::command(
    'subscriptions:process-expiry'
)->everyMinute();

Schedule::command(
    'subscriptions:generate-reminders'
)->hourly();