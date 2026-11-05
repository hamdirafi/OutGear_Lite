<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Schedule::call(fn () => Log::warning('OUTGEAR SCHEDULER: Cron task berjalan setiap 1 menit.'))
    ->everyMinute()
    ->name('ping-scheduler');