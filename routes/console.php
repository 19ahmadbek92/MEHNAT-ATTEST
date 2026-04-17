<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// VM Qarori №263 §33: Har kuni muddatlarni tekshirish
Schedule::command('attestation:check-deadlines')->dailyAt('00:05');

Artisan::command('release:gate', function () {
    Artisan::call('optimize:clear');

    $checks = [
        'test' => ['cmd' => 'test', 'args' => []],
        'migrate:status' => ['cmd' => 'migrate:status', 'args' => []],
        'migrate:dry-run' => ['cmd' => 'migrate', 'args' => ['--pretend' => true]],
        'config:cache' => ['cmd' => 'config:cache', 'args' => []],
        'route:cache' => ['cmd' => 'route:cache', 'args' => []],
        'view:cache' => ['cmd' => 'view:cache', 'args' => []],
    ];

    foreach ($checks as $name => $spec) {
        $this->newLine();
        $this->info(">>> {$name}");
        $exitCode = Artisan::call($spec['cmd'], $spec['args']);
        $this->line(Artisan::output());
        if ($exitCode !== 0) {
            $this->error("Release gate failed on {$name}");
            Artisan::call('optimize:clear');
            return 1;
        }
    }

    $this->newLine();
    $this->warn('Reminder: run `composer audit` on CI for dependency scan.');
    $this->info('Release gate checks passed.');
    Artisan::call('optimize:clear');

    return 0;
})->purpose('Run go-live release gate checks');

