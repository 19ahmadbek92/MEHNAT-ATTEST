<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// VM Qarori №263 §33: Har kuni muddatlarni tekshirish
Schedule::command('attestation:check-deadlines')->dailyAt('00:05');

