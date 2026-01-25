<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('backup:clean')->dailyAt('03:10')->timezone('Europe/Madrid');
Schedule::command('backup:run --only-db')->dailyAt('03:15')->timezone('Europe/Madrid');
// Schedule::command('backup:run')->sundays()->at('03:20')->timezone('Europe/Madrid');
