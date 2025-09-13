<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar notificaciones push cada 30 minutos
Schedule::command('notifications:push')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Programar para días laborales solamente
Schedule::command('notifications:push')
    ->everyFifteenMinutes()
    ->weekdays()
    ->between('8:00', '18:00')
    ->withoutOverlapping()
    ->runInBackground();
